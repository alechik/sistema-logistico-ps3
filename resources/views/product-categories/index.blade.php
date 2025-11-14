@extends('layouts.app')

@section('title', 'Categorías de Productos')

@section('breadcrumb')
    <li class="breadcrumb-item active">Categorías de Productos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Categorías</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#categoryModal">
                            <i class="fas fa-plus"></i> Nueva Categoría
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="categories-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar categoría -->
@include('product-categories.modals.form')

<!-- Modal para confirmar eliminación -->
@include('partials.confirm-delete')

@include('partials.modal-image-preview')

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

<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#categories-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("product-categories.data") }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { 
                data: 'is_active', 
                name: 'is_active',
                render: function(data) {
                    return data ? 
                        '<span class="badge badge-success">Activo</span>' : 
                        '<span class="badge badge-danger">Inactivo</span>';
                }
            },
            {
                data: 'action',
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
        order: [[0, 'asc']],
        responsive: true,
        drawCallback: function() {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // Open modal for new category
    $('.btn-new-category').click(function() {
        $('#categoryForm')[0].reset();
        $('#categoryModal').modal('show');
        $('.modal-title').text('Nueva Categoría');
        $('#categoryForm').attr('data-id', '');
    });

    // Form submission
    $('#categoryForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const categoryId = $(this).data('id');
        const url = categoryId ? 
            `/product-categories/${categoryId}` : 
            '{{ route("product-categories.store") }}';
        const method = categoryId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#categoryModal').modal('hide');
                table.ajax.reload();
                showToast('success', response.message);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(field => {
                        $(`#${field}-error`).text(errors[field][0]);
                    });
                } else {
                    showToast('error', 'Error al guardar la categoría');
                }
            }
        });
    });

    // Edit category
    $('#categories-table').on('click', '.btn-edit', function() {
        const categoryId = $(this).data('id');
        
        $.get(`/product-categories/${categoryId}/edit`, function(response) {
            const category = response.data;
            
            // Fill the form
            $('#categoryForm')[0].reset();
            $('#categoryForm').attr('data-id', category.id);
            $('.modal-title').text('Editar Categoría');
            
            // Set values
            Object.keys(category).forEach(key => {
                $(`#${key}`).val(category[key]);
            });
            
            // Handle active status
            if (category.is_active) {
                $('#is_active').prop('checked', true);
            }
            
            $('#categoryModal').modal('show');
        });
    });

    // Delete category
    $('#categories-table').on('click', '.btn-delete', function() {
        const categoryId = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/product-categories/${categoryId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        table.ajax.reload();
                        showToast('success', response.message);
                    },
                    error: function() {
                        showToast('error', 'Error al eliminar la categoría');
                    }
                });
            }
        });
    });

    // Show toast notification
    function showToast(type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: type,
            title: message
        });
    }

    // Close modal handler
    $('#categoryModal').on('hidden.bs.modal', function () {
        $('#categoryForm')[0].reset();
        $('.invalid-feedback').text('');
        $('.is-invalid').removeClass('is-invalid');
    });
});
</script>
@endpush
