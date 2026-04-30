@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center fw-bold text-primary">
        <i class="bi bi-box-seam"></i> إدارة المنتجات
    </h2>

    {{-- رسائل النجاح --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2" style="font-size:1.2rem;"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    {{-- رسائل الخطأ --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-x-circle-fill me-2" style="font-size:1.2rem;"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
        </div>
    @endif

    {{-- Add Product Button - Only for admin --}}
    @if(auth()->check() && auth()->user()->role === 'admin')
        <div class="mb-3 text-end">
            <a href="{{ route('products.create') }}" class="btn btn-success shadow-sm">
                <i class="bi bi-plus-circle"></i> إضافة منتج جديد
            </a>
        </div>
    @endif

    @if($products->isEmpty())
        <div class="alert alert-warning text-center shadow-sm">
            <i class="bi bi-exclamation-circle"></i> لا توجد منتجات متاحة حالياً
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 product-card">
                    {{-- صورة المنتج --}}
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-img" alt="{{ $product->name }}">
                    @else
                        <div class="card-img-top product-img d-flex align-items-center justify-content-center bg-light"
                             style="height: 220px;">
                            <i class="bi bi-image fs-1 text-muted"></i>
                        </div>
                    @endif

                    <div class="card-body text-end">
                        <h5 class="card-title fw-bold text-primary">
                            <i class="bi bi-tag"></i> {{ $product->name }}
                        </h5>
                        <p class="card-text text-muted">{{ $product->description ?? 'لا يوجد وصف متاح' }}</p>
                        <p class="mb-1"><i class="bi bi-cash-coin text-success"></i> <strong>السعر:</strong> ل.س{{ $product->price }}</p>
                        <p class="mb-1"><i class="bi bi-shop text-info"></i> <strong>المتجر:</strong> {{ $product->store?->name ?? 'غير محدد' }}</p>
                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center bg-light">
                        {{-- Admin Actions - Edit and Delete (Only for admin) --}}
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <div class="d-flex gap-2">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-primary shadow-sm btn-action">
                                    <i class="bi bi-pencil-square"></i> تعديل
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger shadow-sm btn-action">
                                        <i class="bi bi-trash"></i> حذف
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- Add to Cart Button - Only for users --}}
                        @if(auth()->check() && auth()->user()->role === 'user')
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary shadow-sm btn-action">
                                    <i class="bi bi-cart-plus"></i> أضف للسلة
                                </button>
                            </form>
                        @endif

                        {{-- Favorite Button - Only for users --}}
                        @if(auth()->check() && auth()->user()->role === 'user')
                            @php $isFavorite = auth()->check() && in_array($product->id, $favoriteIds ?? []); @endphp
                            <form action="{{ route('favorite.toggle', $product->id) }}" method="POST" class="favorite-form mb-0">
                                @csrf
                                <button type="submit"
                                        class="btn btn-sm shadow-sm btn-action d-flex align-items-center justify-content-center"
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
    .btn-action {
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
                    // Re-enable button on error
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-heart-fill icon-lg ' + (icon && icon.classList.contains('text-danger') ? 'text-danger' : 'text-secondary') + '"></i>';
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
