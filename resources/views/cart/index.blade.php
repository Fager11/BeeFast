@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4 text-center">🛒 سلة التسوق الخاصة بك</h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill ms-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($items->isEmpty())
            <div class="alert alert-info text-center">
                <i class="bi bi-cart-x-fill"></i> سلتك فارغة حالياً.
            </div>
        @else
            <div class="table-responsive mb-4">
                <table class="table table-hover table-bordered align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>المنتج</th>
                            <th>السعر</th>
                            <th>الكمية</th>
                            <th>المجموع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td class="text-end d-flex align-items-center justify-content-start">
                                    <img src="{{ asset('storage/' . $item->product->image) ?? asset('images/default.png') }}"
                                        alt="{{ $item->product->name }}" class="img-thumbnail ms-3"
                                        style="width: 70px; height: 70px; object-fit: cover; border-radius: 12px;">
                                    <div>
                                        <strong>{{ $item->product->name }}</strong><br>
                                        <small class="text-muted">{{ $item->product->description ?? '' }}</small>
                                        <br>
                                        <small class="text-muted">المتجر: {{ $item->product->store->name ?? 'غير محدد' }}</small>
                                    </div>
                                </td>
                                <td class="fw-bold text-success">{{ number_format($item->product->price, 2) }} ل.س</td>
                                <td>
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST"
                                        class="d-flex justify-content-center align-items-center">
                                        @csrf
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                            class="form-control form-control-sm me-2 text-center" style="width: 70px;">
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="تحديث الكمية">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="fw-bold">{{ number_format($item->product->price * $item->quantity, 2) }} ل.س</td>
                                <td>
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                                        onsubmit="return confirm('هل أنت متأكد من حذف المنتج؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف المنتج">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <form action="{{ route('cart.checkout') }}" method="POST" id="checkoutForm">
                @csrf

                {{-- Address and Map Section --}}
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">📍 عنوان التوصيل</h5>
                        <div class="row d-flex align-items-center">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="address" class="form-label">العنوان التفصيلي</label>
                                    <input type="text" name="address" id="address"
                                        class="form-control @error('address') is-invalid @enderror"
                                        placeholder="أدخل عنوان التوصيل هنا..."
                                        value="{{ old('address') }}" required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الموقع على الخريطة</label>
                                <div id="map" style="width:100%; height:200px; border-radius:12px; border:1px solid #ddd;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden inputs for coordinates --}}
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', '33.5138') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', '36.2765') }}">

                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">📦 ملخص الطلب</h5>
                        <div class="row text-center mb-3">
                            <div class="col-md-4">
                                <p>الإجمالي الفرعي:<br><strong class="text-primary">{{ number_format($subtotal, 2) }}
                                        ل.س</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p>رسوم التوصيل:<br><strong class="text-warning">{{ number_format($delivery_fee, 2) }}
                                        ل.س</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p>الخصم:<br><strong class="text-danger">- {{ number_format($discount, 2) }} ل.س</strong>
                                </p>
                            </div>
                        </div>

                        @if ($stores->count() > 0)
                            <div class="mt-3">
                                <h6 class="text-center">🚚 تفاصيل رسوم التوصيل لكل متجر</h6>
                                <div class="alert alert-info text-center mb-2">
                                    <i class="bi bi-info-circle"></i> يتم حساب رسوم التوصيل كمجموع رسوم التوصيل للمتاجر المختلفة في طلبك
                                </div>
                                <table class="table table-sm table-bordered text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المتجر</th>
                                            <th>رسوم التوصيل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stores as $store)
                                            <tr>
                                                <td>{{ $store->name }}</td>
                                                <td>{{ number_format($store->delivery_price, 2) }} ل.س</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active">
                                            <td class="fw-bold">إجمالي رسوم التوصيل</td>
                                            <td class="fw-bold text-warning">{{ number_format($delivery_fee, 2) }} ل.س</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <hr>
                        <h4 class="text-center">💰 الإجمالي الكلي:
                            <strong class="text-success">{{ number_format($total, 2) }} ل.س</strong>
                        </h4>

                        <div class="d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-lg btn-success px-5" id="checkoutButton">
                                <i class="bi bi-cart-check-fill"></i> إتمام عملية الشراء
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>

    <script>
        // Initialize Leaflet map when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Check if map element exists
            const mapElement = document.getElementById('map');
            if (!mapElement) return;

            // Default location (Damascus)
            const defaultLocation = [33.5138, 36.2765];

            // Initialize map
            const map = L.map('map').setView(defaultLocation, 12);

            // Add OpenStreetMap tiles (free!)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Add draggable marker
            const marker = L.marker(defaultLocation, {
                draggable: true
            }).addTo(map);

            // Set initial hidden values
            document.getElementById('latitude').value = defaultLocation[0];
            document.getElementById('longitude').value = defaultLocation[1];

            // Update hidden inputs when marker is dragged
            marker.on('dragend', function(event) {
                const position = marker.getLatLng();
                document.getElementById('latitude').value = position.lat.toFixed(6);
                document.getElementById('longitude').value = position.lng.toFixed(6);
            });

            // Update when map is clicked
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
                document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
            });
        });

        // Keep your existing form validation
        document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
            const address = document.getElementById('address')?.value.trim();
            const lat = document.getElementById('latitude')?.value.trim();
            const lng = document.getElementById('longitude')?.value.trim();

            if (!address || !lat || !lng) {
                e.preventDefault();
                alert('يرجى إدخال عنوان التوصيل وتحديد موقعك على الخريطة قبل إتمام الطلب!');
            }
        });
    </script>
@endsection
