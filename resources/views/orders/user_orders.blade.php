@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-center fw-bold" style="color: var(--clr-deep);">
        <i class="fa fa-bag-shopping ms-2"></i> طلباتي
    </h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-circle-check ms-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-circle-xmark ms-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="alert d-flex align-items-center mb-4 border-0"
         style="background: rgba(249,115,22,0.08); border-right: 4px solid var(--clr-primary) !important; border-radius: 12px;">
        <i class="fa fa-circle-info ms-3" style="color: var(--clr-primary); font-size:1.2rem;"></i>
        <div style="color: var(--clr-text);">
            يمكنك إلغاء الطلب فقط إذا كان في حالة <strong>قيد الانتظار</strong>.
            بعد أن يكون الطلب قيد التنفيذ أو في الطريق أو تم التوصيل، لا يمكن إلغاؤه.
        </div>
    </div>

    <div class="row g-4">
        @forelse($orders as $order)
            @php
                $styles = [
                    'pending'     => ['bg' => 'rgba(249,115,22,0.08)', 'badge' => '#f97316', 'icon' => 'fa-hourglass-half', 'label' => 'قيد الانتظار'],
                    'in_progress' => ['bg' => 'rgba(59,130,246,0.08)', 'badge' => '#3b82f6', 'icon' => 'fa-gear',           'label' => 'قيد التنفيذ'],
                    'on_the_way'  => ['bg' => 'rgba(168,85,247,0.08)', 'badge' => '#a855f7', 'icon' => 'fa-truck',          'label' => 'في الطريق'],
                    'delivered'   => ['bg' => 'rgba(34,197,94,0.08)',  'badge' => '#22c55e', 'icon' => 'fa-circle-check',   'label' => 'تم التوصيل'],
                    'cancelled'   => ['bg' => 'rgba(239,68,68,0.08)',  'badge' => '#ef4444', 'icon' => 'fa-circle-xmark',   'label' => 'تم الإلغاء'],
                ];
                $s = $styles[$order->status] ?? ['bg' => 'rgba(0,0,0,0.04)', 'badge' => '#999', 'icon' => 'fa-bell', 'label' => 'غير معروف'];
            @endphp

            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 order-card"
                     style="background: {{ $s['bg'] }};">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center"
                         style="background: transparent;">
                        <span class="fw-bold" style="color: var(--clr-deep);">
                            <i class="fa {{ $s['icon'] }} ms-2"></i> طلب #{{ $order->id }}
                        </span>
                        <span class="badge rounded-pill px-3"
                              style="background: {{ $s['badge'] }}; color:#fff;">
                            {{ $s['label'] }}
                        </span>
                    </div>

                    <div class="card-body" style="color: var(--clr-text);">
                        <p class="fw-bold" style="color: var(--clr-primary);">
                            <strong>الإجمالي:</strong> ل.س{{ $order->total }}
                        </p>
                        <p><strong>السائق:</strong> {{ $order->driver->name ?? 'لم يتم التعيين بعد' }}</p>
                        <p><strong>عنوان التوصيل:</strong> {{ $order->address ?? 'غير متوفر' }}</p>
                        <p class="mb-1"><strong>المنتجات:</strong></p>
                        <ul>
                            @foreach($order->items as $item)
                                <li>{{ $item->product->name }} × {{ $item->quantity }} (ل.س{{ $item->price }})</li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Map Section --}}
                    @if($order->latitude && $order->longitude)
                        <div class="px-3 pb-2">
                            <div class="border rounded overflow-hidden" style="background: #FEEBD9;">
                                <div id="map-{{ $order->id }}"
                                     style="width:100%; height:180px; border-radius:8px;"></div>
                            </div>
                            <div class="text-center mt-2 small text-muted">
                                <i class="fa fa-location-dot" style="color: #f97316;"></i> موقع توصيل الطلب
                            </div>
                        </div>
                    @else
                        <div class="px-3 pb-2">
                            <div class="text-center text-muted p-2 border rounded" style="background: #FEEBD9;">
                                <i class="fa fa-map fs-5 d-block mb-1"></i>
                                <small>لا يوجد موقع محدد للطلب</small>
                            </div>
                        </div>
                    @endif

                    <div class="card-footer border-0" style="background: transparent;">
                        @if($order->status == 'pending')
                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد أنك تريد إلغاء هذا الطلب؟');">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="fa fa-circle-xmark ms-1"></i> إلغاء الطلب
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                <i class="fa fa-ban ms-1"></i> لا يمكن إلغاء الطلب
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5" style="color: var(--clr-muted);">
                <i class="fa fa-inbox fa-3x mb-3 d-block" style="opacity:.4;"></i>
                لا يوجد لديك أي طلبات حتى الآن
            </div>
        @endforelse
    </div>
</div>

{{-- Leaflet CSS and JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Initialize all maps when page loads
document.addEventListener('DOMContentLoaded', function() {
    @foreach($orders as $order)
        @if($order->latitude && $order->longitude)
            // Check if map element exists
            const mapElement{{ $order->id }} = document.getElementById('map-{{ $order->id }}');
            if (mapElement{{ $order->id }}) {
                // Create map
                const map{{ $order->id }} = L.map('map-{{ $order->id }}').setView(
                    [{{ $order->latitude }}, {{ $order->longitude }}], 14
                );

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map{{ $order->id }});

                // Custom marker icon with Font Awesome
                const orderIcon{{ $order->id }} = L.divIcon({
                    html: '<i class="fa fa-location-dot" style="font-size: 24px; color: #f97316; text-shadow: 0 1px 2px rgba(0,0,0,0.2);"></i>',
                    iconSize: [24, 24],
                    className: 'custom-marker'
                });

                // Add marker for order location
                L.marker([{{ $order->latitude }}, {{ $order->longitude }}], {
                    icon: orderIcon{{ $order->id }}
                })
                .addTo(map{{ $order->id }})
                .bindPopup(`
                    <div style="text-align: right; font-family: inherit; direction: rtl;">
                        <strong>طلب #{{ $order->id }}</strong><br>
                        {{ $order->address ?? 'العنوان غير متوفر' }}<br>
                        الحالة: {{ $s['label'] }}
                    </div>
                `);

                // If order has a driver and driver location is available
                @if($order->driver_id && isset($driverLocations[$order->driver_id]))
                    const driverIcon{{ $order->id }} = L.divIcon({
                        html: '<i class="fa fa-truck" style="font-size: 24px; color: #3b82f6; text-shadow: 0 1px 2px rgba(0,0,0,0.2);"></i>',
                        iconSize: [24, 24],
                        className: 'custom-marker'
                    });

                    L.marker([{{ $driverLocations[$order->driver_id]['lat'] ?? 0 }},
                              {{ $driverLocations[$order->driver_id]['lng'] ?? 0 }}],
                             {icon: driverIcon{{ $order->id }}})
                        .addTo(map{{ $order->id }})
                        .bindPopup(`
                            <div style="text-align: right; font-family: inherit; direction: rtl;">
                                <strong>السائق: {{ $order->driver->name ?? 'غير معروف' }}</strong><br>
                                الموقع الحالي
                            </div>
                        `);

                    // Adjust map to show both markers
                    const bounds{{ $order->id }} = L.latLngBounds([
                        [{{ $order->latitude }}, {{ $order->longitude }}],
                        [{{ $driverLocations[$order->driver_id]['lat'] ?? 0 }}, {{ $driverLocations[$order->driver_id]['lng'] ?? 0 }}]
                    ]);
                    map{{ $order->id }}.fitBounds(bounds{{ $order->id }}, {padding: [30, 30]});
                @endif
            }
        @endif
    @endforeach
});
</script>

<style>
    .order-card {
        border-radius: 14px !important;
        transition: transform .25s, box-shadow .25s;
    }
    .order-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(194,65,12,.15) !important;
    }
    ul {
        padding-right: 20px;
        margin-bottom: 0;
    }
    ul li {
        font-size: .9rem;
        margin-bottom: 2px;
    }

    /* Leaflet map styles */
    .leaflet-container {
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
    }
    .leaflet-control-attribution {
        font-size: 7px;
        background: rgba(255,255,255,0.7);
        border-radius: 4px;
        padding: 1px 3px;
    }
    .leaflet-popup-content {
        text-align: right;
        font-family: 'Cairo', sans-serif;
        min-width: 120px;
        direction: rtl;
        font-size: 12px;
    }
    .custom-marker {
        background: transparent;
    }

    /* Map container hover effect */
    #map-{{ $order->id }} {
        transition: opacity 0.2s ease;
    }
</style>
@endsection
