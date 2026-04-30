@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-center fw-bold text-primary animate__animated animate__fadeInDown">
        <i class="bi bi-shop-window me-2"></i> جميع المتاجر والمنتجات
    </h1>

    <div class="text-end mb-4">
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary shadow-sm">
            <i class="bi bi-card-list"></i> عرض جميع المنتجات
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-x-circle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    @forelse($stores as $store)
        <div class="mb-5 animate__animated animate__fadeInUp">
            <h3 class="d-flex align-items-center mb-3 text-success">
                <i class="bi bi-shop me-2"></i><strong>اسم المتجر:</strong> {{ $store->name }}
            </h3>
            <div class="mb-2 text-muted">
                <span><strong>سعر التوصيل:</strong> ل.س{{ $store->delivery_price }}</span>
                <span class="ms-3"><strong>النوع:</strong> {{ ucfirst($store->type) }}</span>
            </div>

            {{-- Store management actions - Only for admin --}}
            @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="mb-3">
                    <a href="{{ route('products.create', ['store_id' => $store->id]) }}"
                       class="btn btn-success btn-sm shadow-sm">
                        <i class="bi bi-plus-circle"></i> إضافة منتج
                    </a>
                    <a href="{{ route('products.index', ['store_id' => $store->id]) }}"
                       class="btn btn-outline-secondary btn-sm shadow-sm">
                        <i class="bi bi-eye"></i> عرض منتجات المتجر
                    </a>
                </div>
            @endif

            <h5 class="fw-bold text-secondary mt-4 mb-3">
                <i class="bi bi-basket"></i> منتجات المتجر
            </h5>

            @if($store->products->isEmpty())
                <div class="alert alert-warning text-center animate__animated animate__fadeIn">
                    <i class="bi bi-exclamation-triangle"></i> لا توجد منتجات متاحة لهذا المتجر.
                </div>
            @else
                <div class="row g-4">
                    @foreach($store->products as $product)
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0 rounded-3 hover-shadow animate__animated animate__zoomIn product-card">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         class="card-img-top product-img rounded-top"
                                         alt="{{ $product->name }}">
                                @else
                                    <div class="card-img-top product-img rounded-top d-flex align-items-center justify-content-center bg-light"
                                         style="height: 200px;">
                                        <i class="bi bi-image fs-1 text-muted"></i>
                                    </div>
                                @endif

                                <div class="card-body text-end">
                                    <h5 class="card-title fw-bold text-primary">{{ $product->name }}</h5>
                                    <p class="card-text text-muted">{{ $product->description ?? 'لا يوجد وصف' }}</p>
                                    <p class="mb-1"><strong>السعر:</strong> ل.س{{ $product->price }}</p>
                                </div>

                                <div class="card-footer d-flex justify-content-between align-items-center bg-light">
                                    {{-- Admin actions - Edit and Delete --}}
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                        <a href="{{ route('products.edit', $product->id) }}"
                                           class="btn btn-primary btn-sm d-flex align-items-center shadow-sm">
                                            <i class="bi bi-pencil-square me-1"></i> تعديل
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm d-flex align-items-center shadow-sm">
                                                <i class="bi bi-trash me-1"></i> حذف
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Add to Cart button - Only for users --}}
                                    @if(auth()->check() && auth()->user()->role === 'user')
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center shadow-sm">
                                                <i class="bi bi-cart-plus me-1"></i> أضف للسلة
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Favorite button - Only for users --}}
                                    @if(auth()->check() && auth()->user()->role === 'user')
                                        @php
                                            $isFavorite = in_array($product->id, $favoriteIds ?? []);
                                        @endphp
                                        <form action="{{ route('favorite.toggle', $product->id) }}" method="POST" class="favorite-form mb-0">
                                            @csrf
                                            <button type="submit" class="btn btn-light btn-sm d-inline-flex align-items-center justify-content-center rounded-2 icon-btn shadow-sm"
                                                    aria-label="{{ $isFavorite ? 'إزالة من المفضلة' : 'إضافة إلى المفضلة' }}">
                                                <i class="bi bi-heart-fill icon-lg {{ $isFavorite ? 'text-danger' : 'text-secondary' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <div class="alert alert-info text-center animate__animated animate__fadeIn">
            <i class="bi bi-info-circle"></i> لم يتم العثور على متاجر.
        </div>
    @endforelse
</div>
@endsection

@section('styles')
<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}
.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.product-img {
    height: 220px;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.product-card:hover .product-img {
    transform: scale(1.05);
}
.card-footer {
    border-top: none;
}
.btn-action, .icon-btn {
    min-width: 2.8rem;
    min-height: 2.8rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.icon-lg {
    font-size: 1.3rem;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isUser = {{ auth()->check() && auth()->user()->role === 'user' ? 'true' : 'false' }};

    // Only initialize favorite functionality for user role
    if (isUser) {
        document.querySelectorAll('.favorite-form').forEach(function(form){
            form.addEventListener('submit', function(e){
                e.preventDefault();

                const btn = form.querySelector('button');
                const icon = form.querySelector('i.bi-heart-fill');

                // Disable button to prevent double submission
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-heart-fill icon-lg text-secondary"></i>';
                }

                fetch(form.getAttribute('action'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        // Added to favorites
                        if (icon) {
                            icon.classList.remove('text-secondary');
                            icon.classList.add('text-danger');
                        }
                        btn.setAttribute('aria-label', 'إزالة من المفضلة');
                    } else {
                        // Removed from favorites
                        if (icon) {
                            icon.classList.remove('text-danger');
                            icon.classList.add('text-secondary');
                        }
                        btn.setAttribute('aria-label', 'إضافة إلى المفضلة');
                    }

                    // Re-enable button and restore content
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-heart-fill icon-lg ' + (data.status ? 'text-danger' : 'text-secondary') + '"></i>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Re-enable button on error and restore original content
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-heart-fill icon-lg ' + (icon.classList.contains('text-danger') ? 'text-danger' : 'text-secondary') + '"></i>';
                    }
                    // Fallback to form submission
                    form.submit();
                });
            });
        });
    }
});
</script>
@endsection
