@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Ventas</a></li>
    <li class="breadcrumb-item active">Nueva Venta</li>
@endsection

@section('content')
<div class="container-fluid">
    <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Customer and Date -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Datos de la Venta</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_id">Cliente *</label>
                                    <select name="customer_id" id="customer_id" class="form-control select2" required>
                                        <option value="">Seleccione un cliente</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">
                                                {{ $customer->name }} - {{ $customer->document_type }}: {{ $customer->document_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">O deje en blanco para cliente genérico</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sale_date">Fecha de Venta *</label>
                                    <input type="datetime-local" class="form-control" id="sale_date" name="sale_date" 
                                           value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="warehouse_id">Almacén *</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-control select2" required>
                                <option value="">Seleccione un almacén</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">
                                        {{ $warehouse->name }} ({{ $warehouse->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notes">Observaciones</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Productos</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="sale-items-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Producto</th>
                                        <th style="width: 15%;">Cantidad</th>
                                        <th style="width: 15%;">Precio Unit.</th>
                                        <th style="width: 15%;">Descuento</th>
                                        <th style="width: 10%;">Subtotal</th>
                                        <th style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="sale-items">
                                    <!-- Sale items will be added here dynamically -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            <button type="button" class="btn btn-sm btn-primary" id="add-product">
                                                <i class="fas fa-plus"></i> Agregar Producto
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Order Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información de la Orden</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status">Estado de la Orden</label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending" selected>Pendiente</option>
                                <option value="completed">Completada</option>
                                <option value="cancelled">Cancelada</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notas Adicionales</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Resumen</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">S/ 0.00</span>
                            <input type="hidden" name="subtotal" id="subtotal-input" value="0">
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold">
                            <span>Total:</span>
                            <span id="total">S/ 0.00</span>
                            <input type="hidden" name="total" id="total-input" value="0">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block" id="save-sale">
                            <i class="fas fa-save"></i> Guardar Venta
                        </button>
                        <a href="{{ route('sales.index') }}" class="btn btn-default btn-block">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Product Search Modal -->
@include('sales.modals.product-search')

<!-- Customer Modal -->
@include('customers.modals.quick-create')

@endsection

@push('styles')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<!-- Toastr -->
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/toastr/toastr.min.css') }}">
@endpush

@push('scripts')
<!-- Select2 -->
<script src="{{ asset('vendor/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('vendor/adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('vendor/adminlte/plugins/toastr/toastr.min.js') }}"></script>
<!-- Input Mask -->
<script src="{{ asset('vendor/adminlte/plugins/inputmask/jquery.inputmask.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Seleccione una opción'
    });

    // Product counter
    let productCounter = 0;
    
    // Add product button click handler
    $('#add-product').click(function() {
        $('#product-search-modal').modal('show');
    });

    // Form submission
    $('#sale-form').submit(function(e) {
        e.preventDefault();
        
        if (productCounter === 0) {
            showError('Debe agregar al menos un producto a la venta');
            return false;
        }
        
        const formData = new FormData(this);
        
        // Add sale items to form data
        $('.sale-item').each(function(index) {
            const id = $(this).data('id');
            formData.append(`items[${index}][product_id]`, $(this).data('product-id'));
            formData.append(`items[${index}][quantity]`, $(`#quantity_${id}`).val());
            formData.append(`items[${index}][unit_price]`, $(`#price_${id}`).val());
            formData.append(`items[${index}][discount]`, $(`#discount_${id}`).val() || 0);
        });
        
        // Submit form via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showSuccess(response.message);
                setTimeout(() => {
                    window.location.href = '{{ route("sales.index") }}';
                }, 1500);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(field => {
                        showError(errors[field][0]);
                    });
                } else {
                    showError('Error al guardar la venta');
                }
            }
        });
    });

    
    // Show success message
    function showSuccess(message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        
        Toast.fire({
            icon: 'success',
            title: message
        });
    }
    
    // Show error message
    function showError(message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        
        Toast.fire({
            icon: 'error',
            title: message
        });
    }
});
</script>
@endpush
