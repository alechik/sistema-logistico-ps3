@extends('layouts.app')

@section('title', 'Productos con Bajo Stock')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventario</a></li>
    <li class="breadcrumb-item active">Bajo Stock</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lista de Productos con Stock Bajo</h3>
                    <div class="card-tools">
                        <a href="{{ route('inventories.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-arrow-left"></i> Volver al Inventario
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="low-stock-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Almacén</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Diferencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td>{{ $item->warehouse->name ?? 'N/A' }}</td>
                                        <td class="text-danger font-weight-bold">{{ $item->stock ?? $item->quantity }}</td>
                                        <td>{{ $item->min_stock ?? $item->minimum_stock }}</td>
                                        <td class="text-danger">
                                            {{ ($item->min_stock ?? $item->minimum_stock) - ($item->stock ?? $item->quantity) }} unidades
                                        </td>
                                        <td>
                                            <a href="{{ route('inventories.index') }}?warehouse={{ $item->warehouse_id }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Actualizar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No hay productos con bajo stock</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
    $('#low-stock-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        },
        responsive: true,
        order: [[0, 'asc']]
    });
});
</script>
@endpush
