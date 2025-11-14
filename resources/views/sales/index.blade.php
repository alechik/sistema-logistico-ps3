@extends('layouts.app')

@section('title', 'Ventas')

@section('breadcrumb')
    <li class="breadcrumb-item active">Ventas</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Ventas</h3>
                    <div class="card-tools">
                        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nueva Venta
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha Inicio</label>
                                <input type="date" class="form-control" id="start_date" value="{{ date('Y-m-01') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha Fin</label>
                                <input type="date" class="form-control" id="end_date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Estado</label>
                                <select class="form-control" id="status">
                                    <option value="">Todos</option>
                                    <option value="pending">Pendiente</option>
                                    <option value="completed" selected>Completado</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary" id="filter-btn">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            <button class="btn btn-secondary ml-2" id="reset-filters">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="sales-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Total:</th>
                                    <th id="total-sales">S/ 0.00</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles de venta -->
@include('sales.modals.details')

<!-- Modal para anular venta -->
@include('sales.modals.cancel')

@endsection

@push('styles')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<!-- Toastr -->
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/toastr/toastr.min.css') }}">
@endpush

@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{ asset('vendor/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('vendor/adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('vendor/adminlte/plugins/toastr/toastr.min.js') }}"></script>
<!-- Moment -->
<script src="{{ asset('vendor/adminlte/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('vendor/adminlte/plugins/moment/locale/es.js') }}"></script>

<script>
$(document).ready(function() {
    let salesTable;
    
    // Inicializar DataTable
    function initDataTable() {
        salesTable = $('#sales-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: async function(data, callback) {
                try {
                    const startDate = $('#start_date').val();
                    const endDate = $('#end_date').val();
                    const status = $('#status').val();
                    
                    let response;
                    if (startDate && endDate) {
                        response = await window.api.sales.getByDateRange(startDate, endDate);
                    } else {
                        response = await window.api.sales.all();
                    }
                    
                    const sales = (response.data || response).filter(sale => {
                        if (status && sale.status !== status) return false;
                        return true;
                    });
                    
                    callback({
                        data: sales
                    });
                } catch (error) {
                    console.error('Error al cargar ventas:', error);
                    window.apiUtils ? window.apiUtils.handleError(error, 'Error al cargar las ventas') : null;
                    callback({ data: [] });
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { 
                    data: 'sale_date', 
                    name: 'sale_date',
                    render: function(data) {
                        return window.apiUtils ? 
                            window.apiUtils.formatDate(data) : 
                            (typeof moment !== 'undefined' ? moment(data).format('DD/MM/YYYY HH:mm') : data);
                    }
                },
                { 
                    data: 'customer', 
                    name: 'customer',
                    render: function(data, type, row) {
                        if (data) {
                            return data.name || data.email || 'Cliente Genérico';
                        }
                        return row.customer_id ? 'Cliente #' + row.customer_id : 'Cliente Genérico';
                    }
                },
                { 
                    data: 'total', 
                    name: 'total',
                    className: 'text-right',
                    render: function(data) {
                        return window.apiUtils ? 
                            window.apiUtils.formatCurrency(data) : 
                            'S/ ' + parseFloat(data || 0).toFixed(2);
                    }
                },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function(data) {
                        let statusClass = '';
                        let statusText = '';
                        switch(data) {
                            case 'completada':
                            case 'completed':
                                statusClass = 'success';
                                statusText = 'COMPLETADA';
                                break;
                            case 'cancelada':
                            case 'cancelled':
                                statusClass = 'danger';
                                statusText = 'CANCELADA';
                                break;
                            default:
                                statusClass = 'warning';
                                statusText = 'PENDIENTE';
                        }
                        return '<span class="badge badge-' + statusClass + '">' + statusText + '</span>';
                    }
                },
                {
                    data: 'id',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        let buttons = `
                            <button class="btn btn-sm btn-info btn-view" data-id="${row.id}" title="Ver Detalles">
                                <i class="fas fa-eye"></i>
                            </button>`;
                        
                        if (row.status === 'pendiente' || row.status === 'completada' || row.status === 'pending' || row.status === 'completed') {
                            buttons += `
                            <a href="/sales/${row.id}/receipt" class="btn btn-sm btn-secondary" title="Comprobante" target="_blank">
                                <i class="fas fa-file-invoice"></i>
                            </a>`;
                        }
                        
                        if (row.status === 'pendiente' || row.status === 'pending') {
                            buttons += `
                            <button class="btn btn-sm btn-warning btn-cancel" data-id="${row.id}" title="Anular Venta">
                                <i class="fas fa-times"></i>
                            </button>`;
                        }
                        
                        return buttons;
                    }
                }
            ],
            order: [[1, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            responsive: true,
            drawCallback: function() {
                $('[data-toggle="tooltip"]').tooltip();
                
                // Calculate total sales
                if (this.api().data().any()) {
                    const total = this.api().column(3, {search: 'applied'}).data()
                        .reduce((a, b) => {
                            const value = b.replace('S/ ', '').replace(',', '').trim();
                            return a + parseFloat(value || 0);
                        }, 0);
                    $('#total-sales').text(window.apiUtils ? 
                        window.apiUtils.formatCurrency(total) : 
                        'S/ ' + total.toFixed(2));
                }
            }
        });
    }

    // Apply filters
    $('#filter-btn').click(function() {
        salesTable.ajax.reload();
    });

    // Reset filters
    $('#reset-filters').click(function() {
        $('#start_date').val('{{ date("Y-m-01") }}');
        $('#end_date').val('{{ date("Y-m-d") }}');
        $('#status').val('');
        salesTable.ajax.reload();
    });

    // View sale details
    $('#sales-table').on('click', '.btn-view', async function() {
        const saleId = $(this).data('id');
        
        try {
            const response = await window.api.sales.find(saleId);
            const sale = response.data || response;
            const items = sale.items || [];
            
            // Set modal title
            $('#sale-details-title').text(`Venta #${sale.id}`);
            
            // Set sale info
            $('#sale-date').text(window.apiUtils ? 
                window.apiUtils.formatDate(sale.sale_date) : 
                (typeof moment !== 'undefined' ? moment(sale.sale_date).format('DD/MM/YYYY HH:mm') : sale.sale_date));
            $('#sale-status').text(sale.status.toUpperCase());
            
            const customer = sale.customer || {};
            $('#customer-name').text(customer.name || customer.email || 'Cliente Genérico');
            $('#customer-doc').text(customer.document || 'Sin documento');
            $('#sale-notes').text(sale.notes || 'Sin observaciones');
            
            // Build items table
            let itemsHtml = '';
            items.forEach(item => {
                const product = item.product || {};
                itemsHtml += `
                <tr>
                    <td>${product.code || 'N/A'}</td>
                    <td>${product.name || 'Producto'}</td>
                    <td class="text-right">${parseFloat(item.quantity || 0).toFixed(2)}</td>
                    <td class="text-right">${window.apiUtils ? 
                        window.apiUtils.formatCurrency(item.unit_price) : 
                        'S/ ' + parseFloat(item.unit_price || 0).toFixed(2)}</td>
                    <td class="text-right">${window.apiUtils ? 
                        window.apiUtils.formatCurrency(item.total || item.subtotal) : 
                        'S/ ' + parseFloat(item.total || item.subtotal || 0).toFixed(2)}</td>
                </tr>`;
            });
            
            $('#sale-items tbody').html(itemsHtml);
            
            // Set totals
            $('#subtotal').text(window.apiUtils ? 
                window.apiUtils.formatCurrency(sale.subtotal) : 
                'S/ ' + parseFloat(sale.subtotal || 0).toFixed(2));
            $('#tax').text(window.apiUtils ? 
                window.apiUtils.formatCurrency(sale.tax) : 
                'S/ ' + parseFloat(sale.tax || 0).toFixed(2));
            $('#total').text(window.apiUtils ? 
                window.apiUtils.formatCurrency(sale.total) : 
                'S/ ' + parseFloat(sale.total || 0).toFixed(2));
            
            // Show modal
            $('#sale-details-modal').modal('show');
        } catch (error) {
            window.apiUtils ? window.apiUtils.handleError(error, 'Error al cargar los detalles de la venta') : console.error(error);
        }
    });

    // Cancel sale
    $('#sales-table').on('click', '.btn-cancel', async function() {
        const saleId = $(this).data('id');
        
        const confirmed = window.apiUtils ? 
            await window.apiUtils.confirm('¿Está seguro de anular esta venta? Esta acción no se puede deshacer.', 'Anular Venta') :
            confirm('¿Está seguro de anular esta venta?');
        
        if (confirmed) {
            try {
                await window.api.sales.cancel(saleId);
                salesTable.ajax.reload();
                if (window.apiUtils) {
                    window.apiUtils.showSuccess('Venta anulada correctamente');
                }
            } catch (error) {
                window.apiUtils ? window.apiUtils.handleError(error, 'Error al anular la venta') : console.error(error);
            }
        }
    });

    // Inicializar
    initDataTable();
});
</script>
@endpush
