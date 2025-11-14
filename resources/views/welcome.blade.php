@extends('adminlte::page')

@section('title', 'Bienvenido')

@push('css')
    <style>
        .welcome-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 1rem 0;
        }
        
        .welcome-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .welcome-content {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
@endpush

@section('content_header')
    <h1 class="m-0">Bienvenido a {{ config('app.name') }}</h1>
@stop

@section('content')
    <div class="welcome-content">
        <div class="welcome-card">
            <div class="welcome-header">
                <h2>Sistema de Gestión Logística</h2>
                <p class="lead">Una solución integral para la gestión de inventario y logística</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Acceso Rápido</h3>
                        </div>
                        <div class="card-body">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-block mb-3">Ir al Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-block mb-3">Iniciar Sesión</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-block">Registrarse</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Características</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success mr-2"></i> Gestiona tu inventario</li>
                                <li><i class="fas fa-check text-success mr-2"></i> Control de almacenes</li>
                                <li><i class="fas fa-check text-success mr-2"></i> Reportes en tiempo real</li>
                                <li><i class="fas fa-check text-success mr-2"></i> Interfaz intuitiva</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="welcome-card mt-4">
            <h3>Acerca del Sistema</h3>
            <p>Este sistema ha sido desarrollado con las siguientes tecnologías:</p>
            
            <div class="row text-center mt-4">
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="fab fa-laravel fa-3x text-danger"></i>
                        <div class="mt-2">Laravel 12</div>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="fab fa-php fa-3x text-primary"></i>
                        <div class="mt-2">PHP 8.2+</div>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="fab fa-js-square fa-3x text-warning"></i>
                        <div class="mt-2">JavaScript</div>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="fab fa-bootstrap fa-3x text-primary"></i>
                        <div class="mt-2">Bootstrap 5</div>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="fas fa-database fa-3x text-info"></i>
                        <div class="mt-2">PostgreSQL</div>
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <div class="p-3">
                        <i class="fas fa-tachometer-alt fa-3x text-success"></i>
                        <div class="mt-2">AdminLTE 3</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        console.log('Página de bienvenida cargada correctamente');
    </script>
@endpush
