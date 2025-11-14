@extends('layouts.app')

@section('title', 'Gestión de Inventario')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inventario</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Inventario</h3>
                    <div class="card-tools">
                        <a href="{{ route('inventories.low-stock') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-exclamation-triangle"></i> Bajo Stock
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Almacén</label>
                                <select class="form-control" id="warehouse-filter">
                                    <option value="">Todos los almacenes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Producto</label>
                                <input type="text" class="form-control" id="product-search" placeholder="Buscar producto...">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary" id="filter-btn">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            <button class="btn btn-secondary ml-2" id="reset-filters">
                                <i class="fas fa-undo"></i> Limpiar
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="inventories-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Almacén</th>
                                    <th>Stock</th>
                                    <th>Stock Mínimo</th>
                                    <th>Stock Máximo</th>
                                    <th>Costo Unitario</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán vía API -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para actualizar stock -->
<div class="modal fade" id="updateStockModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Stock</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="updateStockForm">
                <div class="modal-body">
                    <input type="hidden" id="inventory-id">
                    <div class="form-group">
                        <label>Nueva Cantidad</label>
                        <input type="number" class="form-control" id="new-quantity" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Notas</label>
                        <textarea class="form-control" id="stock-notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<script>
$(document).ready(function() {
    let inventoriesTable;
    let warehouses = [];

    // Cargar almacenes
    async function loadWarehouses() {
        try {
            const response = await window.api.warehouses.all();
            warehouses = response.data || response;
            const select = $('#warehouse-filter');
            select.empty().append('<option value="">Todos los almacenes</option>');
            warehouses.forEach(warehouse => {
                select.append(`<option value="${warehouse.id}">${warehouse.name}</option>`);
            });
        } catch (error) {
            console.error('Error al cargar almacenes:', error);
        }
    }

    // Inicializar DataTable
    function initDataTable() {
        inventoriesTable = $('#inventories-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: async function(data, callback) {
                try {
                    const warehouseId = $('#warehouse-filter').val();
                    const productSearch = $('#product-search').val();
                    
                    let response;
                    if (warehouseId) {
                        response = await window.api.inventories.getByWarehouse(warehouseId);
                    } else {
                        response = await window.api.inventories.all();
                    }
                    
                    let inventories = response.data || response;
                    
                    // Filtrar por búsqueda de producto
                    if (productSearch) {
                        inventories = inventories.filter(inv => {
                            const product = inv.product || {};
                            const name = (product.name || '').toLowerCase();
                            const code = (product.code || '').toLowerCase();
                            const search = productSearch.toLowerCase();
                            return name.includes(search) || code.includes(search);
                        });
                    }
                    
                    callback({ data: inventories });
                } catch (error) {
                    console.error('Error al cargar inventario:', error);
                    window.apiUtils ? window.apiUtils.handleError(error, 'Error al cargar el inventario') : null;
                    callback({ data: [] });
                }
            },
            columns: [
                { 
                    data: 'product', 
                    name: 'product',
                    render: function(data) {
                        if (data) {
                            return `<strong>${data.name || 'N/A'}</strong><br><small>${data.code || ''}</small>`;
                        }
                        return 'N/A';
                    }
                },
                { 
                    data: 'warehouse', 
                    name: 'warehouse',
                    render: function(data) {
                        return data ? data.name : 'N/A';
                    }
                },
                { 
                    data: 'stock', 
                    name: 'stock',
                    className: 'text-center',
                    render: function(data, type, row) {
                        const stock = data || row.quantity || 0;
                        const minStock = row.min_stock || row.minimum_stock || 0;
                        const badgeClass = stock <= minStock ? 'danger' : 'success';
                        return `<span class="badge badge-${badgeClass}">${stock}</span>`;
                    }
                },
                { 
                    data: 'min_stock', 
                    name: 'min_stock',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return data || row.minimum_stock || 0;
                    }
                },
                { 
                    data: 'max_stock', 
                    name: 'max_stock',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return data || row.maximum_stock || '-';
                    }
                },
                { 
                    data: 'unit_cost', 
                    name: 'unit_cost',
                    className: 'text-right',
                    render: function(data) {
                        return window.apiUtils ? 
                            window.apiUtils.formatCurrency(data || 0) : 
                            'S/ ' + parseFloat(data || 0).toFixed(2);
                    }
                },
                { 
                    data: 'stock', 
                    name: 'status',
                    className: 'text-center',
                    render: function(data, type, row) {
                        const stock = data || row.quantity || 0;
                        const minStock = row.min_stock || row.minimum_stock || 0;
                        if (stock <= minStock) {
                            return '<span class="badge badge-warning">Bajo Stock</span>';
                        }
                        return '<span class="badge badge-success">Normal</span>';
                    }
                },
                {
                    data: 'id',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-primary btn-update-stock" data-id="${row.id}" data-stock="${row.stock || row.quantity}" title="Actualizar Stock">
                                <i class="fas fa-edit"></i>
                            </button>
                        `;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            order: [[0, 'asc']],
            responsive: true
        });
    }

    // Filtrar
    $('#filter-btn').click(function() {
        inventoriesTable.ajax.reload();
    });

    // Limpiar filtros
    $('#reset-filters').click(function() {
        $('#warehouse-filter').val('');
        $('#product-search').val('');
        inventoriesTable.ajax.reload();
    });

    // Actualizar stock
    $('#inventories-table').on('click', '.btn-update-stock', function() {
        const inventoryId = $(this).data('id');
        const currentStock = $(this).data('stock');
        
        $('#inventory-id').val(inventoryId);
        $('#new-quantity').val(currentStock);
        $('#stock-notes').val('');
        $('#updateStockModal').modal('show');
    });

    // Enviar formulario de actualización
    $('#updateStockForm').submit(async function(e) {
        e.preventDefault();
        
        const inventoryId = $('#inventory-id').val();
        const quantity = parseFloat($('#new-quantity').val());
        const notes = $('#stock-notes').val();
        
        try {
            await window.api.inventories.updateStock(inventoryId, {
                quantity: quantity,
                notes: notes
            });
            
            $('#updateStockModal').modal('hide');
            inventoriesTable.ajax.reload();
            if (window.apiUtils) {
                window.apiUtils.showSuccess('Stock actualizado correctamente');
            }
        } catch (error) {
            window.apiUtils ? window.apiUtils.handleError(error, 'Error al actualizar el stock') : console.error(error);
        }
    });

    // Inicializar
    loadWarehouses();
    initDataTable();
});
</script>
@endpush

