<div class="sidebar">
    <div class="sidebar-header">
        <h3>{{ config('app.name') }}</h3>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-header">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
            </ul>
        </div>

        <!-- Módulo de Gestión de Usuarios -->
        <div class="nav-section">
            <div class="nav-section-header">
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-user-tag"></i> Tipos de Usuario
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                </li>
            </ul>
        </div>

        <!-- Módulo de Almacenes -->
        <div class="nav-section">
            <div class="nav-section-header">
                <i class="fas fa-warehouse"></i>
                <span>Almacenes</span>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-tags"></i> Tipos de Almacén
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-warehouse"></i> Almacenes
                    </a>
                </li>
            </ul>
        </div>

        <!-- Módulo de Inventario -->
        <div class="nav-section">
            <div class="nav-section-header">
                <i class="fas fa-boxes"></i>
                <span>Inventario</span>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-tags"></i> Categorías
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-box"></i> Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-clipboard-list"></i> Inventario
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-exchange-alt"></i> Movimientos
                    </a>
                </li>
            </ul>
        </div>

        <!-- Módulo de Ventas -->
        <div class="nav-section">
            <div class="nav-section-header">
                <i class="fas fa-shopping-cart"></i>
                <span>Ventas</span>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cash-register"></i> Nueva Venta
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-list"></i> Historial
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-bar"></i> Reportes
                    </a>
                </li>
            </ul>
        </div>

        <!-- Módulo de Configuración -->
        <div class="nav-section">
            <div class="nav-section-header">
                <i class="fas fa-cog"></i>
                <span>Configuración</span>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-sliders-h"></i> Ajustes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-user-cog"></i> Perfil
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>
