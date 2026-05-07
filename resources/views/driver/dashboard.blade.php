@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h1 class="mb-4 text-center fw-bold" style="color: var(--clr-deep);">
        <i class="fa fa-truck ms-2"></i> لوحة تحكم السائق
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

   
    <div class="card border-0 shadow mb-4">
        <div class="card-header">
            <i class="fa fa-bell ms-2"></i> الإشعارات
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse(($orders ?? []) as $order)
                    <li class="list-group-item d-flex justify-content-between align-items-center"
                        style="background: transparent; color: var(--clr-text);">
                        <span>
                            الطلب رقم <strong>#{{ $order->id }}</strong>
                            من <span class="fw-bold" style="color: var(--clr-deep);">
                                {{ $order->user->name ?? 'مستخدم غير معروف' }}
                            </span>
                        </span>
                        <span class="badge rounded-pill px-3" style="
                            background: {{
                                $order->status == 'pending'     ? '#f97316' :
                                ($order->status == 'in_progress' ? '#3b82f6' :
                                ($order->status == 'on_the_way'  ? '#a855f7' :
                                ($order->status == 'delivered'   ? '#22c55e' : '#ef4444')))
                            }}; color:#fff;">
                            {{ ['pending'=>'قيد الانتظار','in_progress'=>'قيد التنفيذ',
                                'on_the_way'=>'في الطريق','delivered'=>'تم التوصيل',
                                'cancelled'=>'ملغى'][$order->status] ?? $order->status }}
                        </span>
                    </li>
                @empty
                    <li class="list-group-item text-center" style="color: var(--clr-muted);">
                        لا توجد طلبات مسندة بعد
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    
    <div class="d-flex flex-column gap-4">
        @forelse($orders as $order)
            @php
                $styles = match($order->status) {
                    'pending'     => ['bg'=>'rgba(249,115,22,0.08)', 'badge'=>'#f97316', 'label'=>'قيد الانتظار'],
                    'in_progress' => ['bg'=>'rgba(59,130,246,0.08)', 'badge'=>'#3b82f6', 'label'=>'قيد التنفيذ'],
                    'on_the_way'  => ['bg'=>'rgba(168,85,247,0.08)', 'badge'=>'#a855f7', 'label'=>'في الطريق'],
                    'delivered'   => ['bg'=>'rgba(34,197,94,0.08)',  'badge'=>'#22c55e', 'label'=>'تم التوصيل'],
                    'cancelled'   => ['bg'=>'rgba(239,68,68,0.08)',  'badge'=>'#ef4444', 'label'=>'ملغى'],
                    default       => ['bg'=>'rgba(0,0,0,0.04)',      'badge'=>'#999',    'label'=>'غير معروف'],
                };
            @endphp

            <div class="card border-0 shadow"
                 style="background: {{ $styles['bg'] }};">
                <div class="card-header border-0 d-flex justify-content-between align-items-center"
                     style="background: transparent;">
                    <span class="fw-bold" style="color: var(--clr-deep);">
                        <i class="fa fa-hashtag ms-1"></i> الطلب رقم {{ $order->id }}
                    </span>
                    <span class="badge rounded-pill px-3"
                          style="background: {{ $styles['badge'] }}; color:#fff;">
                        {{ $styles['label'] }}
                    </span>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p><strong>المستخدم:</strong> {{ $order->user->name }}</p>
                            <p><strong>العنوان:</strong> {{ $order->address }}</p>
                            <p><strong>المجموع الجزئي:</strong> ل.س{{ $order->subtotal }}</p>
                            <p><strong>رسوم التوصيل:</strong> ل.س{{ $order->delivery_price }}</p>
                            <p><strong>الخصم:</strong> ل.س{{ $order->discount }}</p>
                            <p class="fw-bold" style="color: var(--clr-primary);">
                                <strong>الإجمالي:</strong> ل.س{{ $order->total }}
                            </p>

                            <p class="fw-bold mb-1">المنتجات:</p>
                            <ul class="mb-3">
                                @foreach($order->items as $item)
                                    <li>{{ $item->product->name }} × {{ $item->quantity }} (ل.س{{ $item->price }})</li>
                                @endforeach
                            </ul>

                            @if(auth()->user()->role == 'driver' && in_array($order->status, ['in_progress','on_the_way']))
                                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST"
                                      class="d-flex gap-2 align-items-center mt-2">
                                    @csrf
                                    <select name="status" class="form-select form-select-sm" style="width:auto;">
                                        <option value="">-- تحديث الحالة --</option>
                                        <option value="on_the_way" @selected($order->status=='on_the_way')>في الطريق</option>
                                        <option value="delivered"  @selected($order->status=='delivered')>تم التوصيل</option>
                                    </select>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fa fa-rotate ms-1"></i> تحديث
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="col-md-4 mt-3 mt-md-0">
                            <div id="map-{{ $order->id }}"
                                 style="width:100%; height:200px; border-radius:12px;
                                        border:1px solid rgba(249,115,22,0.2);"></div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5" style="color: var(--clr-muted);">
                <i class="fa fa-inbox fa-3x mb-3 d-block" style="opacity:.4;"></i>
                لا توجد طلبات مسندة بعد
            </div>
        @endforelse
    </div>
</div>

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

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map{{ $order->id }});

            // Add marker for order location
            L.marker([{{ $order->latitude }}, {{ $order->longitude }}])
                .addTo(map{{ $order->id }})
                .bindPopup('موقع الطلب #{{ $order->id }}');

            // Optional: Add driver's current location if available
            @if(auth()->user()->role == 'driver' && $order->driver_id == auth()->id())
                // You can add a second marker for driver location if you have it
                // L.marker([driver_lat, driver_lng], {icon: driverIcon}).addTo(map{{ $order->id }});
            @endif
        }
    @endforeach
});

// Optional: Handle any AJAX updates if needed
function refreshOrderStatus(orderId) {
    // You can implement AJAX calls here to update order status
    // and refresh specific maps
}
</script>

{{-- Add some custom styles for Leaflet maps --}}
<style>
.leaflet-container {
    border-radius: 12px;
    font-family: inherit;
}
.leaflet-control-attribution {
    font-size: 9px;
    background: rgba(255,255,255,0.8);
    border-radius: 4px;
    padding: 2px 5px;
}
</style>
@endsection
