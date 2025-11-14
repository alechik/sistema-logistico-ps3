@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Panel de Control</h1>
@stop

@section('content')
    <div class="row">
        <!-- Total Products -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    @php
                        $productCount = \Schema::hasTable('products') ? \App\Models\Product::count() : 0;
                    @endphp
                    <h3>{{ $productCount }}</h3>
                    <p>Productos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <a href="{{ route('products.index') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    @php
                        $lowStockCount = 0;
                        if (\Schema::hasTable('inventories')) {
                            $minQuantity = \App\Models\Inventory::min('quantity');
                            if ($minQuantity !== null) {
                                $lowStockCount = \App\Models\Inventory::where('quantity', '<=', $minQuantity * 1.2)->count();
                            }
                        }
                    @endphp
                    <h3>{{ $lowStockCount }}</h3>
                    <p>Productos con bajo stock</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('inventories.low-stock') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    @php
                        $totalSales = \Schema::hasTable('sales') ? \App\Models\Sale::sum('total') : 0;
                    @endphp
                    <h3>${{ number_format($totalSales, 2) }}</h3>
                    <p>Ventas totales</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('sales.index') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Total Warehouses -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    @php
                        $warehouseCount = \Schema::hasTable('warehouses') ? \App\Models\Warehouse::count() : 0;
                    @endphp
                    <h3>{{ $warehouseCount }}</h3>
                    <p>Almacenes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <a href="{{ route('warehouses.index') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    @if(\Schema::hasTable('sales'))
    <!-- Recent Sales -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ventas recientes</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $recentSales = \App\Models\Sale::latest()->take(5)->get();
                            @endphp
                            @forelse($recentSales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td>${{ number_format($sale->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay ventas recientes</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(\Schema::hasTable('inventories') && \Schema::hasTable('products') && \Schema::hasTable('warehouses'))
    <!-- Low Stock Items -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Productos con bajo stock</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Almacén</th>
                                <th>Stock actual</th>
                                <th>Stock mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $lowStockItems = \App\Models\Inventory::with(['product', 'warehouse'])
                                    ->whereColumn('quantity', '<=', 'minimum_stock')
                                    ->get();
                            @endphp
                            
                            @forelse($lowStockItems as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->warehouse->name ?? 'N/A' }}</td>
                                <td class="text-danger font-weight-bold">{{ $item->quantity }}</td>
                                <td>{{ $item->minimum_stock }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay productos con bajo stock</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
@stop

@push('js')
    <script>
        // Auto-refresh the dashboard every 5 minutes
        setTimeout(function(){
            window.location.reload();
        }, 300000);
    </script>
@endpush
