@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center fw-bold" style="color: var(--clr-deep);">
        <i class="fa fa-list-check ms-2"></i> إدارة الطلبات
    </h2>

    <div class="d-flex flex-column gap-4">
        @forelse($orders as $order)
        @php
            $styles = match($order->status) {
                'pending'     => ['bg' => 'rgba(249,115,22,0.08)',  'border' => 'rgba(249,115,22,0.3)',  'badge' => '#f97316', 'label' => 'قيد الانتظار'],
                'in_progress' => ['bg' => 'rgba(59,130,246,0.08)',  'border' => 'rgba(59,130,246,0.3)',  'badge' => '#3b82f6', 'label' => 'قيد التنفيذ'],
                'on_the_way'  => ['bg' => 'rgba(168,85,247,0.08)', 'border' => 'rgba(168,85,247,0.3)', 'badge' => '#a855f7', 'label' => 'في الطريق'],
                'delivered'   => ['bg' => 'rgba(34,197,94,0.08)',   'border' => 'rgba(34,197,94,0.3)',   'badge' => '#22c55e', 'label' => 'تم التوصيل'],
                'cancelled'   => ['bg' => 'rgba(239,68,68,0.08)',   'border' => 'rgba(239,68,68,0.3)',   'badge' => '#ef4444', 'label' => 'ملغى'],
                default       => ['bg' => 'rgba(0,0,0,0.04)',       'border' => 'rgba(0,0,0,0.1)',       'badge' => '#999',    'label' => 'غير معروف'],
            };
        @endphp

        <div class="card border-0 shadow-sm p-3"
             style="background: {{ $styles['bg'] }}; border: 1px solid {{ $styles['border'] }} !important; transition: all .3s;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold" style="color: var(--clr-deep);">
                    <i class="fa fa-hashtag ms-1"></i> الطلب رقم {{ $order->id }}
                </span>
                <span class="badge rounded-pill px-3 py-2"
                      style="background: {{ $styles['badge'] }}; color:#fff;">
                    {{ $styles['label'] }}
                </span>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <p><strong>المستخدم:</strong> {{ $order->user->name }}</p>
                    <p><strong>العنوان:</strong> {{ $order->address }}</p>
                    <p><strong>المجموع الجزئي:</strong> ل.س{{ $order->subtotal }}</p>
                    <p><strong>رسوم التوصيل:</strong> ل.س{{ $order->delivery_price }}</p>
                    <p><strong>الخصم:</strong> ل.س{{ $order->discount }}</p>
                    <p><strong>الإجمالي:</strong> <span class="fw-bold" style="color: var(--clr-primary);">ل.س{{ $order->total }}</span></p>

                    <p><strong>المنتجات:</strong></p>
                    <ul class="mb-3">
                        @foreach($order->items as $item)
                            <li>{{ $item->product->name }} × {{ $item->quantity }} (ل.س{{ $item->price }})</li>
                        @endforeach
                    </ul>

                    @if(auth()->user()->role == 'admin')
                        <div class="d-flex flex-column gap-2 mt-2">
                            <form action="{{ route('orders.assignDriver', $order->id) }}" method="POST"
                                  class="d-flex gap-2 flex-wrap align-items-center">
                                @csrf
                                <select name="driver_id" class="form-select form-select-sm" style="width:auto;">
                                    <option value="">اختر السائق</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}"
                                            @selected($order->driver_id == $driver->id)>
                                            {{ $driver->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary btn-sm">
                                    <i class="fa fa-user-check ms-1"></i> تعيين السائق وقبول
                                </button>
                            </form>

                            @if($order->status == 'pending')
                                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST"
                                      class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="cancelled">
                                    <button class="btn btn-danger btn-sm">
                                        <i class="fa fa-ban ms-1"></i> رفض الطلب
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    @if(auth()->user()->role == 'driver' && in_array($order->status, ['in_progress','on_the_way']))
                        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST"
                              class="d-flex gap-2 align-items-center mt-2">
                            @csrf
                            <select name="status" class="form-select form-select-sm" style="width:auto;">
                                <option value="">-- تحديث الحالة --</option>
                                <option value="on_the_way" @selected($order->status=='on_the_way')>في الطريق</option>
                                <option value="delivered"  @selected($order->status=='delivered')>تم التوصيل</option>
                            </select>
                            <button class="btn btn-sm btn-primary">
                                <i class="fa fa-rotate ms-1"></i> تحديث
                            </button>
                        </form>
                    @endif
                </div>

                <div class="col-md-4 mt-3 mt-md-0">
                    <div id="map-{{ $order->id }}"
                         style="width:100%; height:200px; border-radius:12px; border:1px solid rgba(249,115,22,0.2);">
                    </div>
                </div>
            </div>
        </div>
        @empty
            <div class="text-center py-5" style="color: var(--clr-muted);">
                <i class="fa fa-inbox fa-3x mb-3 d-block" style="opacity:.4;"></i>
                لا توجد طلبات حتى الآن
            </div>
        @endforelse
    </div>
</div>

{{-- Replace Google Maps with Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Initialize all maps when page loads
document.addEventListener('DOMContentLoaded', function() {
    @foreach($orders as $order)
        // Check if map element exists
        const mapElement{{ $order->id }} = document.getElementById('map-{{ $order->id }}');
        if (mapElement{{ $order->id }}) {
            // Create map
            const map{{ $order->id }} = L.map('map-{{ $order->id }}').setView(
                [{{ $order->latitude }}, {{ $order->longitude }}], 14
            );

            // Add OpenStreetMap tiles (free!)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map{{ $order->id }});

            // Add marker for order location
            const marker{{ $order->id }} = L.marker([{{ $order->latitude }}, {{ $order->longitude }}])
                .addTo(map{{ $order->id }})
                .bindPopup(`
                    <div style="text-align: right; font-family: inherit;">
                        <strong>الطلب #{{ $order->id }}</strong><br>
                        {{ $order->address }}<br>
                        الحالة: {{ $styles['label'] }}
                    </div>
                `);

            // Optional: Add a circle to show delivery radius if needed
            // L.circle([{{ $order->latitude }}, {{ $order->longitude }}], {
            //     color: '{{ $styles['badge'] }}',
            //     fillColor: '{{ $styles['badge'] }}',
            //     fillOpacity: 0.1,
            //     radius: 500
            // }).addTo(map{{ $order->id }});

            // If order is assigned to a driver and we have driver location
            @if($order->driver_id && isset($driverLocations[$order->driver_id]))
                // You can add a second marker for the driver
                const driverIcon{{ $order->id }} = L.divIcon({
                    html: '🚚',
                    className: 'driver-marker',
                    iconSize: [30, 30]
                });

                L.marker([{{ $driverLocations[$order->driver_id]['lat'] ?? 0 }},
                          {{ $driverLocations[$order->driver_id]['lng'] ?? 0 }}],
                         {icon: driverIcon{{ $order->id }}})
                    .addTo(map{{ $order->id }})
                    .bindPopup('موقع السائق');
            @endif
        }
    @endforeach
});

// Optional: Function to refresh maps with new data via AJAX
function refreshOrderMaps() {
    // You can implement AJAX calls here to update driver positions
    // and refresh specific maps
    console.log('Maps can be refreshed here');
}
</script>

{{-- Add custom styles for Leaflet maps --}}
<style>
.leaflet-container {
    border-radius: 12px;
    font-family: 'Cairo', sans-serif;
}
.leaflet-control-attribution {
    font-size: 8px;
    background: rgba(255,255,255,0.7);
    border-radius: 4px;
    padding: 2px 5px;
}
.leaflet-popup-content {
    text-align: right;
    font-family: 'Cairo', sans-serif;
    min-width: 150px;
}
.driver-marker {
    background: transparent;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
