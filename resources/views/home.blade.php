@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('custom-css/home.css') }}">
@endpush

@section('content')
    <div class="container py-4">
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill ms-2" style="font-size:1.2rem;"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-x-circle-fill ms-2" style="font-size:1.2rem;"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        
        <div class="text-center mb-5">
            <img src="{{ asset('img/logo-en.png') }}" alt="logo" width="160px" height="160px" class="mb-3"
                style="filter: drop-shadow(0 4px 12px rgba(249,115,22,0.3));">
            <h2 class="fw-bold" style="color: var(--clr-deep, #2c3e50);">تابع طلبك لحظة بلحظة</h2>
        </div>

        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-geo-alt-fill ms-2"></i> معرفة موقع طلبك الحالي
                            </h5>
                            <div class="text-end small" style="color: var(--clr-muted, #6c757d);">
                                @php $driver_phone = $orders->first()?->driver?->phone; @endphp
                                <div>رقم السائق: <strong
                                        style="color: var(--clr-primary, #f97316);">{{ $driver_phone ?? 'لم يتم تعيين السائق بعد' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body py-4">
                        
                        <div class="order-tracker">
                            <div class="tracker-line">
                                @php
                                    $currentStatus = $orders->first()?->status;
                                    $progressPercent = 0;
                                    if ($currentStatus == 'pending') {
                                        $progressPercent = 25;
                                    } elseif ($currentStatus == 'in_progress') {
                                        $progressPercent = 50;
                                    } elseif ($currentStatus == 'on_the_way') {
                                        $progressPercent = 75;
                                    } elseif ($currentStatus == 'delivered') {
                                        $progressPercent = 100;
                                    }
                                @endphp
                                <div class="tracker-line-fill" style="width: {{ $progressPercent }}%;"></div>
                            </div>

                            <div class="tracker-steps">
                                
                                <div
                                    class="tracker-step {{ in_array($orders->first()?->status, ['pending', 'in_progress', 'on_the_way', 'delivered']) ? 'active' : '' }}">
                                    <div class="step-circle">
                                        <img src="https://static.thenounproject.com/png/1588606-512.png" alt="قيد الانتظار">
                                    </div>
                                    <p class="step-label">الطلب<br>قيد الانتظار</p>
                                </div>

                                
                                <div
                                    class="tracker-step {{ in_array($orders->first()?->status, ['in_progress', 'on_the_way', 'delivered']) ? 'active' : '' }}">
                                    <div class="step-circle">
                                        <img src="https://static.thenounproject.com/png/2030977-512.png" alt="قيد العمل">
                                    </div>
                                    <p class="step-label">الطلب<br>قيد العمل</p>
                                </div>

                                
                                <div
                                    class="tracker-step {{ in_array($orders->first()?->status, ['on_the_way', 'delivered']) ? 'active' : '' }}">
                                    <div class="step-circle">
                                        <img src="https://static.thenounproject.com/png/4934446-512.png" alt="في الطريق">
                                    </div>
                                    <p class="step-label">الطلب<br>في الطريق</p>
                                </div>

                                
                                <div
                                    class="tracker-step {{ in_array($orders->first()?->status, ['delivered']) ? 'active' : '' }}">
                                    <div class="step-circle">
                                        <img src="https://static.thenounproject.com/png/4849084-512.png" alt="تم التوصيل">
                                    </div>
                                    <p class="step-label">الطلب<br>تم التوصيل</p>
                                </div>
                            </div>
                        </div>

                        
                        @php $currentOrder = $orders->first(); @endphp
                        @if ($currentOrder && $currentOrder->latitude && $currentOrder->longitude)
                            <div class="mt-4">
                                <div class="border rounded overflow-hidden" style="background: #FEEBD9;">
                                    <div id="tracked-order-map" style="width:100%; height:300px;"></div>
                                </div>
                                <div class="text-center mt-2 small text-muted">
                                    <i class="bi bi-geo-alt-fill" style="color: #f97316;"></i> موقع توصيل الطلب
                                </div>
                            </div>
                        @else
                            <div class="mt-4 text-center text-muted p-4 border rounded" style="background: #FEEBD9;">
                                <i class="bi bi-map fs-1 d-block mb-2"></i>
                                <p>لا يوجد موقع محدد للطلب الحالي</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        
        <div class="container py-4 mt-4">
            <h1 class="mb-4 text-center">طلباتي</h1>

            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-info-circle-fill ms-2" style="font-size:1.2rem;"></i>
                <div class="me-1">
                    يمكنك إلغاء الطلب فقط إذا كان في حالة <strong>قيد الانتظار</strong>.
                    ولا يمكن إلغاء الطلب <strong>قيد التنفيذ</strong> أو <strong>في الطريق</strong> أو <strong>تم
                        التوصيل</strong>.
                </div>
            </div>

            <div id="orders-list" class="row g-4">
                @forelse($orders as $order)
                    @php
                        $statusColors = [
                            'pending' => ['bg' => '#fff8e1', 'text' => '#6c4f00', 'icon' => 'bi-hourglass-split'],
                            'in_progress' => ['bg' => '#e3f2fd', 'text' => '#0d47a1', 'icon' => 'bi-gear-fill'],
                            'on_the_way' => ['bg' => '#fce4ec', 'text' => '#880e4f', 'icon' => 'bi-truck'],
                            'delivered' => ['bg' => '#e8f5e9', 'text' => '#1b5e20', 'icon' => 'bi-check-circle-fill'],
                            'cancelled' => ['bg' => '#ffebee', 'text' => '#b71c1c', 'icon' => 'bi-x-circle-fill'],
                        ];

                        $statusTranslations = [
                            'pending' => 'قيد الانتظار',
                            'in_progress' => 'قيد التنفيذ',
                            'on_the_way' => 'في الطريق',
                            'delivered' => 'تم التوصيل',
                            'cancelled' => 'تم الإلغاء',
                        ];

                        $color = $statusColors[$order->status]['bg'] ?? '#f5f5f5';
                        $textColor = $statusColors[$order->status]['text'] ?? '#333';
                        $icon = $statusColors[$order->status]['icon'] ?? 'bi-bell-fill';
                        $statusText = $statusTranslations[$order->status] ?? $order->status;
                    @endphp

                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm h-100" style="background: {{ $color }};">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                style="background: transparent; border: none;">
                                <span class="fw-bold"><i class="bi {{ $icon }} mx-2"></i>طلب
                                    #{{ $order->id }}</span>
                                <span class="badge" style="background: {{ $textColor }}; color: #fff;">
                                    {{ $statusText }}
                                </span>
                            </div>
                            <div class="card-body">
                                <p><strong>المتجر:</strong> {{ $order->store->name ?? 'غير متوفر' }}</p>
                                <p><strong>الإجمالي:</strong> ل.س{{ $order->total }}</p>
                                <p><strong>السائق:</strong> {{ $order->driver->name ?? 'لم يتم التعيين بعد' }}</p>
                                <p><strong>المنتجات:</strong></p>
                                <ul>
                                    @foreach ($order->items as $item)
                                        <li>{{ $item->product->name }} × {{ $item->quantity }} (ل.س{{ $item->price }})
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-footer d-flex flex-column gap-2">
                                @if ($order->status == 'pending')
                                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST"
                                        onsubmit="return confirm('هل أنت متأكد أنك تريد إلغاء هذا الطلب؟');">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-x-circle me-1"></i>إلغاء الطلب
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        <i class="bi bi-slash-circle me-1"></i>إلغاء الطلب غير متاح
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center">لا يوجد لديك أي طلبات حتى الآن.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @php $currentOrder = $orders->first(); @endphp
            @if ($currentOrder && $currentOrder->latitude && $currentOrder->longitude)
                // Create map for tracked order
                const mapElement = document.getElementById('tracked-order-map');
                if (mapElement) {
                    const trackedMap = L.map('tracked-order-map').setView(
                        [{{ $currentOrder->latitude }}, {{ $currentOrder->longitude }}],
                        15
                    );

                    // Add OpenStreetMap tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                        maxZoom: 19
                    }).addTo(trackedMap);

                    // Custom marker icon (optional)
                    const orderIcon = L.divIcon({
                        html: '<i class="bi bi-geo-alt-fill" style="font-size: 32px; color: #f97316; text-shadow: 0 2px 4px rgba(0,0,0,0.2);"></i>',
                        iconSize: [32, 32],
                        className: 'custom-marker'
                    });

                    // Add marker for order location
                    L.marker([{{ $currentOrder->latitude }}, {{ $currentOrder->longitude }}], {
                            icon: orderIcon
                        })
                        .addTo(trackedMap)
                        .bindPopup(`
                            <div style="text-align: center;">
                                <strong>طلب #{{ $currentOrder->id }}</strong><br>
                                <span style="color: #f97316;">موقع التوصيل</span>
                            </div>
                        `)
                        .openPopup();

                    // If order is on_the_way or in_progress, you could add a driver location marker
                    @if (in_array($currentOrder->status, ['in_progress', 'on_the_way']) &&
                            $currentOrder->driver &&
                            $currentOrder->driver->latitude &&
                            $currentOrder->driver->longitude)
                        const driverIcon = L.divIcon({
                            html: '<i class="bi bi-truck" style="font-size: 32px; color: #3b82f6; text-shadow: 0 2px 4px rgba(0,0,0,0.2);"></i>',
                            iconSize: [32, 32],
                            className: 'custom-marker'
                        });

                        L.marker([{{ $currentOrder->driver->latitude }},
                            {{ $currentOrder->driver->longitude }}], {
                                icon: driverIcon
                            })
                            .addTo(trackedMap)
                            .bindPopup(`
                                <div style="text-align: center;">
                                    <strong>السائق: {{ $currentOrder->driver->name }}</strong><br>
                                    <span style="color: #3b82f6;">الموقع الحالي</span>
                                </div>
                            `);
                    @endif
                }
            @endif
        });
    </script>

@endsection
@push('styles')
    <style>
       
        .order-tracker {
            position: relative;
            padding: 0 10px;
        }

       
        .tracker-line {
            position: absolute;
            top: 30px;
            right: 10%;
            left: 10%;
            height: 4px;
            background: rgba(249, 115, 22, 0.15);
            border-radius: 2px;
            z-index: 0;
        }

       
        .tracker-line-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #f97316, #ef4444);
            border-radius: 2px;
            transition: width .6s ease;
        }

        
        .tracker-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 100;
        }

        .tracker-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        
        .step-circle {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: #FEEBD9;
            border: 3px solid rgba(249, 115, 22, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            transition: all .3s ease;
            z-index: 100;
        }

        .step-circle img {
            width: 34px;
            height: 34px;
            opacity: 0.4;
            filter: grayscale(1);
            transition: all .3s ease;
        }

        
        .tracker-step.active .step-circle {
            background: #FEEBD9;
            border-color: #f97316;
            box-shadow: 0 0 0 5px rgba(249, 115, 22, 0.12);
        }

        .tracker-step.active .step-circle img {
            opacity: 1;
            filter: none;
        }

       
        .step-label {
            font-size: 0.82rem;
            font-weight: 600;
            text-align: center;
            color: #6c757d;
            line-height: 1.4;
            margin: 0;
        }

        .tracker-step.active .step-label {
            color: #2c3e50;
        }

        
        @media (max-width: 576px) {
            .step-circle {
                width: 48px;
                height: 48px;
            }

            .step-circle img {
                width: 26px;
                height: 26px;
            }

            .step-label {
                font-size: 0.72rem;
            }
        }

        /* Styles for orders list */
        ul {
            padding-left: 20px;
            margin-bottom: 0;
        }

        ul li {
            font-size: 0.9rem;
            margin-bottom: 2px;
        }

        /* Map styles */
        .leaflet-container {
            border-radius: 8px;
            font-family: inherit;
        }
    </style>
@endpush
