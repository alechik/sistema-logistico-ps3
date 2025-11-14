@extends('adminlte::page')

@section('title', 'Módulo de Inventario')

@section('content_header')
    <h1>Dashboard de Inventario</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Bienvenido al Módulo de Inventario</h3>
                    </div>
                    <div class="card-body">
                        <p>Este es el panel de control del módulo de inventario.</p>
                        <p>Aquí podrás gestionar todos los aspectos relacionados con el inventario de tu sistema logístico.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Módulo de Inventario cargado correctamente');
    </script>
@stop
