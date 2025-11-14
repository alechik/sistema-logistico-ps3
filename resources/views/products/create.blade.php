@extends('layouts.app')

@section('title', 'Crear Producto')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">Nuevo Producto</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nuevo Producto</h3>
                </div>
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        @include('products._form')
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@push('scripts')
<!-- Select2 -->
<script src="{{ asset('vendor/adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
@endpush
