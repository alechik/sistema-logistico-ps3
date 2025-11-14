@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('breadcrumb')
    <li class="breadcrumb-item active">Productos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Productos</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#productModal" id="btn-new-product">
                            <i class="fas fa-plus"></i> Nuevo Producto
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="products-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán vía AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar producto -->
@include('products.modals.form')

<!-- Modal para confirmar eliminación -->
@include('partials.confirm-delete')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let productsTable;
    
    // Inicializar DataTable
    function initDataTable() {
        productsTable = $('#products-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/api/v1/products',
                dataSrc: function(json) {
                    return json.data || json;
                }
            },
            columns: [
                { data: 'code', name: 'code' },
                { data: 'name', name: 'name' },
                { 
                    data: 'category', 
                    name: 'category',
                    render: function(data, type, row) {
                        return data ? data.name : 'Sin categoría';
                    }
                },
                { 
                    data: 'sale_price', 
                    name: 'sale_price',
                    render: function(data) {
                        return window.apiUtils ? window.apiUtils.formatCurrency(data) : 'S/ ' + parseFloat(data || 0).toFixed(2);
                    }
                },
                { 
                    data: 'stock', 
                    name: 'stock',
                    className: 'text-center'
                },
                { 
                    data: 'is_active', 
                    name: 'is_active',
                    className: 'text-center',
                    render: function(data) {
                        return data ? 
                            '<span class="badge badge-success">Activo</span>' : 
                            '<span class="badge badge-danger">Inactivo</span>';
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
                            <button class="btn btn-sm btn-info btn-edit" data-id="${row.id}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            order: [[1, 'asc']],
            responsive: true
        });
    }

    // Cargar categorías para el select
    async function loadCategories() {
        try {
            const response = await window.api.productCategories.all();
            const categories = response.data || response;
            const select = $('#product_category_id');
            select.empty().append('<option value="">Seleccione una categoría</option>');
            categories.forEach(cat => {
                select.append(`<option value="${cat.id}">${cat.name}</option>`);
            });
        } catch (error) {
            console.error('Error al cargar categorías:', error);
        }
    }

    // Abrir modal para crear producto
    $('#btn-new-product').click(function() {
        $('#productForm')[0].reset();
        $('#productForm').removeAttr('data-id');
        $('#productModal .modal-title').text('Nuevo Producto');
        $('#current-image').empty();
        loadCategories();
        $('#productModal').modal('show');
    });

    // Enviar formulario
    $('#productForm').submit(async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const productId = $(this).data('id');
        
        // Convertir FormData a objeto
        const data = {};
        formData.forEach((value, key) => {
            if (key === 'is_active') {
                data[key] = value === '1' || value === true;
            } else {
                data[key] = value;
            }
        });

        try {
            let response;
            if (productId) {
                response = await window.api.products.update(productId, data);
            } else {
                response = await window.api.products.create(data);
            }
            
            $('#productModal').modal('hide');
            productsTable.ajax.reload();
            if (window.apiUtils) {
                window.apiUtils.showSuccess(response.message || 'Producto guardado correctamente');
            }
        } catch (error) {
            window.apiUtils ? window.apiUtils.handleError(error, 'Error al guardar el producto') : console.error(error);
        }
    });

    // Editar producto
    $('#products-table').on('click', '.btn-edit', async function() {
        const productId = $(this).data('id');
        
        try {
            const response = await window.api.products.find(productId);
            const product = response.data || response;
            
            // Llenar el formulario
            $('#productForm')[0].reset();
            $('#productForm').attr('data-id', product.id);
            $('#productModal .modal-title').text('Editar Producto');
            
            // Llenar campos
            $('#code').val(product.code);
            $('#name').val(product.name);
            $('#description').val(product.description);
            $('#product_category_id').val(product.product_category_id || product.category_id);
            $('#purchase_price').val(product.purchase_price);
            $('#sale_price').val(product.sale_price);
            $('#stock').val(product.stock);
            $('#min_stock').val(product.min_stock);
            $('#barcode').val(product.barcode);
            $('#unit').val(product.unit || 'UNIDAD');
            $('#is_active').prop('checked', product.is_active !== false);
            
            // Mostrar imagen actual si existe
            if (product.image_url) {
                $('#current-image').html(`
                    <img src="${product.image_url}" class="img-fluid" style="max-height: 100px;">
                `);
            } else {
                $('#current-image').empty();
            }
            
            await loadCategories();
            $('#product_category_id').val(product.product_category_id || product.category_id).trigger('change');
            $('#productModal').modal('show');
        } catch (error) {
            window.apiUtils ? window.apiUtils.handleError(error, 'Error al cargar el producto') : console.error(error);
        }
    });

    // Eliminar producto
    $('#products-table').on('click', '.btn-delete', async function() {
        const productId = $(this).data('id');
        
        const confirmed = window.apiUtils ? 
            await window.apiUtils.confirm('¿Está seguro de eliminar este producto?', 'Eliminar Producto') :
            confirm('¿Está seguro de eliminar este producto?');
        
        if (confirmed) {
            try {
                await window.api.products.delete(productId);
                productsTable.ajax.reload();
                if (window.apiUtils) {
                    window.apiUtils.showSuccess('Producto eliminado correctamente');
                }
            } catch (error) {
                window.apiUtils ? window.apiUtils.handleError(error, 'Error al eliminar el producto') : console.error(error);
            }
        }
    });

    // Inicializar
    initDataTable();
    loadCategories();
});
</script>
@endpush
