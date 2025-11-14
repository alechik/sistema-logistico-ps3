@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inicio</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Productos</span>
                    <span class="info-box-number" id="total-products">-</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-warehouse"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Almacenes</span>
                    <span class="info-box-number" id="total-warehouses">-</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Ventas</span>
                    <span class="info-box-number" id="total-sales">-</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Bajo Stock</span>
                    <span class="info-box-number" id="low-stock-items">-</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <div class="col-md-8">
            <!-- Recent Sales -->
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Ventas Recientes</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0" id="recent-sales-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">Cargando...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right col -->
        <div class="col-md-4">
            <!-- Low Stock Items -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Productos con Bajo Stock</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2" id="low-stock-list">
                        <li class="item">
                            <div class="product-info">
                                <span class="product-description">
                                    Cargando...
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(async function() {
    // Cargar estadísticas
    async function loadStats() {
        try {
            // Cargar productos
            const productsResponse = await window.api.products.all();
            const products = productsResponse.data || productsResponse;
            $('#total-products').text(products.length || 0);

            // Cargar almacenes
            const warehousesResponse = await window.api.warehouses.all();
            const warehouses = warehousesResponse.data || warehousesResponse;
            $('#total-warehouses').text(warehouses.length || 0);

            // Cargar ventas
            const salesResponse = await window.api.sales.all();
            const sales = salesResponse.data || salesResponse;
            $('#total-sales').text(sales.length || 0);

            // Cargar inventario con stock bajo
            const lowStockResponse = await window.api.inventories.getLowStock();
            const lowStock = lowStockResponse.data || lowStockResponse;
            $('#low-stock-items').text(lowStock.length || 0);

            // Cargar ventas recientes
            const recentSales = sales.slice(0, 5);
            let salesHtml = '';
            if (recentSales.length > 0) {
                recentSales.forEach(sale => {
                    const date = window.apiUtils ? 
                        window.apiUtils.formatDate(sale.sale_date, 'DD/MM/YYYY HH:mm') : 
                        new Date(sale.sale_date).toLocaleString('es-PE');
                    const total = window.apiUtils ? 
                        window.apiUtils.formatCurrency(sale.total) : 
                        'S/ ' + parseFloat(sale.total || 0).toFixed(2);
                    const status = sale.status === 'completada' || sale.status === 'completed' ? 
                        '<span class="badge badge-success">Completada</span>' : 
                        '<span class="badge badge-warning">Pendiente</span>';
                    
                    salesHtml += `
                        <tr>
                            <td>#${sale.id}</td>
                            <td>${date}</td>
                            <td>${total}</td>
                            <td>${status}</td>
                        </tr>`;
                });
            } else {
                salesHtml = '<tr><td colspan="4" class="text-center">No hay ventas recientes</td></tr>';
            }
            $('#recent-sales-table tbody').html(salesHtml);

            // Cargar productos con bajo stock
            let lowStockHtml = '';
            if (lowStock.length > 0) {
                lowStock.slice(0, 5).forEach(item => {
                    const product = item.product || {};
                    const warehouse = item.warehouse || {};
                    lowStockHtml += `
                        <li class="item">
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">
                                    ${product.name || 'Producto'}
                                    <span class="badge badge-warning float-right">${item.stock || item.quantity} / ${item.min_stock || item.minimum_stock}</span>
                                </a>
                                <span class="product-description">
                                    ${warehouse.name || 'Almacén'}
                                </span>
                            </div>
                        </li>`;
                });
            } else {
                lowStockHtml = `
                    <li class="item">
                        <div class="product-info">
                            <span class="product-description">
                                No hay productos con bajo stock
                            </span>
                        </div>
                    </li>`;
            }
            $('#low-stock-list').html(lowStockHtml);
        } catch (error) {
            console.error('Error al cargar estadísticas:', error);
            if (window.apiUtils) {
                window.apiUtils.handleError(error, 'Error al cargar las estadísticas');
            }
        }
    }

    // Inicializar
    await loadStats();
    
    // Recargar cada 5 minutos
    setInterval(loadStats, 300000);
});
</script>
@endpush
@endsection
