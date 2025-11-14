@extends('layouts.app')

@section('title', 'Almacenes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Almacenes</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Almacenes</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#warehouseModal">
                            <i class="fas fa-plus"></i> Nuevo Almacén
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="warehouses-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Código</th>
                                    <th>Tipo</th>
                                    <th>Ubicación</th>
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

<!-- Modal para crear/editar almacén -->
@include('warehouses.modals.form')

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
    let warehousesTable;
    let warehouseTypes = [];

    // Cargar tipos de almacén
    async function loadWarehouseTypes() {
        try {
            const response = await window.api.warehouseTypes.all();
            warehouseTypes = response.data || response;
            const select = $('#warehouse_type_id');
            if (select.length) {
                select.empty().append('<option value="">Seleccione un tipo</option>');
                warehouseTypes.forEach(type => {
                    select.append(`<option value="${type.id}">${type.name}</option>`);
                });
            }
        } catch (error) {
            console.error('Error al cargar tipos de almacén:', error);
        }
    }

    // Inicializar DataTable
    function initDataTable() {
        warehousesTable = $('#warehouses-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: async function(data, callback) {
                try {
                    const response = await window.api.warehouses.all();
                    const warehouses = response.data || response;
                    callback({ data: warehouses });
                } catch (error) {
                    console.error('Error al cargar almacenes:', error);
                    window.apiUtils ? window.apiUtils.handleError(error, 'Error al cargar los almacenes') : null;
                    callback({ data: [] });
                }
            },
            columns: [
                { data: 'name', name: 'name' },
                {
                    data: 'code',
                    name: 'code',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'warehouse_type',
                    name: 'warehouse_type',
                    render: function(data) {
                        return data ? data.name : '-';
                    }
                },
                {
                    data: 'location',
                    name: 'location',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        return data !== false ?
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
                            <a href="/inventories?warehouse=${row.id}" class="btn btn-sm btn-info" title="Ver Inventario">
                                <i class="fas fa-boxes"></i>
                            </a>
                            <button class="btn btn-sm btn-warning btn-edit" data-id="${row.id}" title="Editar">
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
    }

    // Abrir modal para crear almacén
    $('[data-target="#warehouseModal"]').click(function() {
        $('#warehouseForm')[0].reset();
        $('#warehouseForm').removeAttr('data-id');
        $('#warehouseModal .modal-title').text('Nuevo Almacén');
        loadWarehouseTypes();
        $('#warehouseModal').modal('show');
    });

    // Enviar formulario
    $('#warehouseForm').submit(async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const warehouseId = $(this).data('id');

        const data = {};
        formData.forEach((value, key) => {
            if (key === 'status') {
                data[key] = value === '1' || value === true;
            } else {
                data[key] = value;
            }
        });

        try {
            let response;
            if (warehouseId) {
                response = await window.api.warehouses.update(warehouseId, data);
            } else {
                response = await window.api.warehouses.create(data);
            }

            $('#warehouseModal').modal('hide');
            warehousesTable.ajax.reload();
            if (window.apiUtils) {
                window.apiUtils.showSuccess(response.message || 'Almacén guardado correctamente');
            }
        } catch (error) {
            window.apiUtils ? window.apiUtils.handleError(error, 'Error al guardar el almacén') : console.error(error);
        }
    });

    // Editar almacén
    $('#warehouses-table').on('click', '.btn-edit', async function() {
        const warehouseId = $(this).data('id');

        try {
            const response = await window.api.warehouses.find(warehouseId);
            const warehouse = response.data || response;

            $('#warehouseForm')[0].reset();
            $('#warehouseForm').attr('data-id', warehouse.id);
            $('#warehouseModal .modal-title').text('Editar Almacén');

            $('#name').val(warehouse.name);
            $('#location').val(warehouse.location);
            $('#email').val(warehouse.email);
            $('#cellphone').val(warehouse.cellphone);
            $('#warehouse_type_id').val(warehouse.warehouse_type_id);
            $('#status').prop('checked', warehouse.status !== false);

            await loadWarehouseTypes();
            $('#warehouse_type_id').val(warehouse.warehouse_type_id).trigger('change');
            $('#warehouseModal').modal('show');
        } catch (error) {
            window.apiUtils ? window.apiUtils.handleError(error, 'Error al cargar el almacén') : console.error(error);
        }
    });

    // Eliminar almacén
    $('#warehouses-table').on('click', '.btn-delete', async function() {
        const warehouseId = $(this).data('id');

        const confirmed = window.apiUtils ?
            await window.apiUtils.confirm('¿Está seguro de eliminar este almacén?', 'Eliminar Almacén') :
            confirm('¿Está seguro de eliminar este almacén?');

        if (confirmed) {
            try {
                await window.api.warehouses.delete(warehouseId);
                warehousesTable.ajax.reload();
                if (window.apiUtils) {
                    window.apiUtils.showSuccess('Almacén eliminado correctamente');
                }
            } catch (error) {
                window.apiUtils ? window.apiUtils.handleError(error, 'Error al eliminar el almacén') : console.error(error);
            }
        }
    });

    // Inicializar
    loadWarehouseTypes();
    initDataTable();
});
</script>
@endpush
