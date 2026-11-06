@extends('layouts.app')

@section('content')
<div class="container py-4">

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

    
    <div class="card mb-4 border-0 shadow">
        <div class="card-body d-flex align-items-center gap-4 flex-wrap">
            @if($store->image)
                <img src="{{ asset('storage/' . $store->image) }}"
                     alt="{{ $store->name }}"
                     class="rounded-3 shadow-sm"
                     style="width:140px; height:140px; object-fit:cover;">
            @else
                <div class="rounded-3 shadow-sm d-flex align-items-center justify-content-center"
                     style="width:140px; height:140px; background: rgba(249,115,22,0.08);">
                    <i class="fa fa-store fa-3x" style="color: var(--clr-primary);"></i>
                </div>
            @endif

            <div class="flex-grow-1">
                <h3 class="fw-bold mb-2" style="color: var(--clr-deep);">{{ $store->name }}</h3>
                <p class="mb-1">
                    <i class="fa fa-money-bill ms-1" style="color: var(--clr-primary);"></i>
                    <strong>سعر التوصيل:</strong> {{ $store->delivery_price }} ل.س
                </p>
                <p class="mb-1">
                    <i class="fa fa-store ms-1" style="color: var(--clr-primary);"></i>
                    <strong>النوع:</strong>
                    @switch($store->type)
                        @case('restaurant') مطعم @break
                        @case('cafe')       مقهى @break
                        @default            متجر
                    @endswitch
                </p>
                <p class="mb-0">
                    <i class="fa fa-star ms-1" style="color: var(--clr-accent);"></i>
                    <strong>الأكثر شهرة:</strong> {{ $store->popular ? 'نعم' : 'لا' }}
                </p>
            </div>

            {{-- Admin actions - Only for admin role --}}
            @if(Auth::check() && Auth::user()->role === 'admin')
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-pen ms-1"></i> تعديل المتجر
                    </a>
                    <form action="{{ route('stores.destroy', $store->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100"
                                onclick="return confirm('هل أنت متأكد من حذف هذا المتجر؟')">
                            <i class="fa fa-trash ms-1"></i> حذف المتجر
                        </button>
                    </form>
                    <a href="{{ route('products.create', $store->id) }}" class="btn btn-sm"
                       style="background: #22c55e; color:#fff;">
                        <i class="fa fa-plus ms-1"></i> إضافة منتج
                    </a>
                </div>
            @endif
        </div>
    </div>

    
    <h4 class="fw-bold mb-3" style="color: var(--clr-deep);">
        <i class="fa fa-box ms-2"></i> المنتجات
    </h4>

    <div class="row g-3">
        @forelse($products as $product)
            <div class="col-md-4 col-sm-6">
                <div class="card product-card h-100 border-0 shadow-sm">

                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             class="card-img-top product-img" alt="{{ $product->name }}">
                    @else
                        <div class="card-img-top product-img d-flex align-items-center justify-content-center"
                             style="background: rgba(249,115,22,0.08);">
                            <i class="fa fa-image fa-3x" style="color: var(--clr-light);"></i>
                        </div>
                    @endif

                    <div class="card-body">
                        <h5 class="fw-bold mb-1" style="color: var(--clr-deep);">{{ $product->name }}</h5>
                        <p class="small mb-2" style="color: var(--clr-muted);">
                            {{ $product->description ?? 'لا يوجد وصف' }}
                        </p>
                        <p class="mb-0 fw-bold" style="color: var(--clr-primary);">
                            {{ $product->price }} ل.س
                        </p>
                    </div>

                    <div class="card-footer border-0 d-flex justify-content-between align-items-center"
                         style="background: transparent;">

                        {{-- Add to Cart - Only for users --}}
                        @if(auth()->check() && auth()->user()->role === 'user')
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa fa-cart-plus ms-1"></i> أضف للسلة
                                </button>
                            </form>
                        @endif

                        <div class="d-flex gap-1">
                            {{-- Favorite button - Only for users --}}
                            @if(auth()->check() && auth()->user()->role === 'user')
                                @php
                                    $isFavorite = in_array($product->id, $favoriteIds ?? []);
                                @endphp
                                <form action="{{ route('favorite.toggle', $product->id) }}" method="POST"
                                      class="favorite-form d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light rounded-2"
                                            title="{{ $isFavorite ? 'إزالة من المفضلة' : 'إضافة إلى المفضلة' }}">
                                        <i class="fa fa-heart" style="color: {{ $isFavorite ? 'var(--clr-accent)' : '#ccc' }};"></i>
                                    </button>
                                </form>
                            @endif

                            {{-- Admin actions - Edit and Delete --}}
                            @if(auth()->check() && auth()->user()->role === 'admin')
                                <a href="{{ route('products.edit', $product->id) }}"
                                   class="btn btn-sm btn-light rounded-2" title="تعديل">
                                    <i class="fa fa-pen" style="color: var(--clr-primary);"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light rounded-2"
                                            title="حذف"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                        <i class="fa fa-trash" style="color: var(--clr-accent);"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5" style="color: var(--clr-muted);">
                <i class="fa fa-box-open fa-3x mb-3 d-block" style="opacity:.4;"></i>
                لا توجد منتجات في هذا المتجر بعد
            </div>
        @endforelse
    </div>
</div>

<style>
    .product-card {
        border-radius: 14px !important;
        overflow: hidden;
        transition: transform .25s, box-shadow .25s;
    }
    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 25px rgba(194,65,12,.15) !important;
    }
    .product-img {
        height: 200px;
        object-fit: cover;
        transition: transform .4s;
    }
    .product-card:hover .product-img {
        transform: scale(1.05);
    }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isUser = {{ auth()->check() && auth()->user()->role === 'user' ? 'true' : 'false' }};

    // Only initialize favorite functionality for user role
    if (isUser) {
        document.querySelectorAll('.favorite-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const col = form.closest('.col-md-4, .col-sm-6');
                const btn = form.querySelector('button');
                const icon = btn ? btn.querySelector('i') : null;

                // Disable button and show loading state
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                }

                fetch(form.action, {
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
                            icon.style.color = 'var(--clr-accent)';
                        }
                        if (btn) {
                            btn.setAttribute('title', 'إزالة من المفضلة');
                        }
                    } else {
                        // Removed from favorites
                        if (icon) {
                            icon.style.color = '#ccc';
                        }
                        if (btn) {
                            btn.setAttribute('title', 'إضافة إلى المفضلة');
                        }
                    }

                    // Re-enable button and restore content
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa fa-heart" style="color: ' + (data.status ? 'var(--clr-accent)' : '#ccc') + ';"></i>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Re-enable button on error
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa fa-heart" style="color: ' + (icon && icon.style.color === 'rgb(249, 115, 22)' ? 'var(--clr-accent)' : '#ccc') + ';"></i>';
                    }
                    // Fallback to form submission
                    form.submit();
                });
            });
        });
    }
});
</script>
{{-- ===== قسم التقييمات ===== --}}
<div class="container mt-5">

    {{-- متوسط التقييم --}}
    <div class="d-flex align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0" style="color: var(--clr-deep);">
            <i class="fa fa-star ms-2" style="color:#f97316;"></i> التقييمات
        </h4>
        @if($reviewCount > 0)
            <span class="badge rounded-pill px-3 py-2"
                  style="background: rgba(249,115,22,0.15); color: var(--clr-deep);">
                {{ number_format($averageRating, 1) }} / 5
                ({{ $reviewCount }} تقييم)
            </span>
        @endif
    </div>

    {{-- نموذج إضافة تقييم --}}
    @auth
        @if(auth()->user()->role === 'user')
            @if($userReview)
                <div class="alert border-0 shadow-sm mb-4"
                     style="background: rgba(249,115,22,0.08); border-radius:12px;">
                    <p class="mb-1 fw-bold">تقييمك الحالي:
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star"
                               style="color: {{ $i <= $userReview->rating ? '#f97316' : '#ddd' }};"></i>
                        @endfor
                    </p>
                    @if($userReview->comment)
                        <p class="mb-2 small">{{ $userReview->comment }}</p>
                    @endif
                    <form action="{{ route('reviews.destroy', $store->id) }}"
                          method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fa fa-trash ms-1"></i> حذف تقييمي
                        </button>
                    </form>
                </div>

            @elseif($canReview)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header">
                        <i class="fa fa-star ms-2"></i> أضف تقييمك
                    </div>
                    <div class="card-body">
                        <form action="{{ route('reviews.store', $store->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">التقييم</label>
                                <div class="star-rating d-flex gap-2 flex-row-reverse justify-content-end">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating"
                                               id="star{{ $i }}" value="{{ $i }}"
                                               style="display:none;">
                                        <label for="star{{ $i }}"
                                               class="fa fa-star fs-4"
                                               style="cursor:pointer; color:#ddd;"></label>
                                    @endfor
                                </div>
                                @error('rating')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label fw-bold">
                                    تعليق (اختياري)
                                </label>
                                <textarea name="comment" id="comment"
                                          class="form-control" rows="3"
                                          placeholder="شاركنا رأيك...">{{ old('comment') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-paper-plane ms-1"></i> إرسال التقييم
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @endif
    @endauth

    {{-- عرض كل التقييمات --}}
    @if($reviews->isEmpty())
        <p class="text-muted text-center py-3">لا توجد تقييمات بعد لهذا المتجر.</p>
    @else
        <div class="d-flex flex-column gap-3 mb-4">
            @foreach($reviews as $review)
                <div class="card border-0 shadow-sm" style="border-radius:12px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold" style="color:var(--clr-deep);">
                                {{ $review->user->name }}
                            </span>
                            <span>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa fa-star"
                                       style="color: {{ $i <= $review->rating ? '#f97316' : '#ddd' }};"></i>
                                @endfor
                            </span>
                        </div>
                        @if($review->comment)
                            <p class="mb-1 small" style="color:var(--clr-text);">
                                {{ $review->comment }}
                            </p>
                        @endif
                        <small style="color:var(--clr-muted);">
                            {{ $review->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.star-rating label { transition: color 0.2s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #f97316 !important;
}
</style>
@endsection
@endsection
