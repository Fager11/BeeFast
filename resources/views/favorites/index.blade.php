@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-center fw-bold" style="color: var(--clr-deep);">
        <i class="fa fa-heart ms-2" style="color: var(--clr-accent);"></i> المفضلة
    </h1>

    @if($favorites->isEmpty())
        <div class="text-center py-5" style="color: var(--clr-muted);">
            <i class="fa fa-heart fa-4x mb-3 d-block" style="opacity:.3; color: var(--clr-accent);"></i>
            <p class="fs-5">لم تقم بإضافة أي منتجات إلى المفضلة بعد</p>
        </div>
    @else
        <div class="row g-3">
            @foreach($favorites as $product)
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
                            <p class="fw-bold mb-1" style="color: var(--clr-primary);">
                                {{ $product->price }} ل.س
                            </p>
                            @if($product->store)
                                <p class="small mb-0" style="color: var(--clr-muted);">
                                    <i class="fa fa-store ms-1"></i> {{ $product->store->name }}
                                </p>
                            @endif
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
                                {{-- Remove from Favorites - Only for users --}}
                                @if(auth()->check() && auth()->user()->role === 'user')
                                    <form action="{{ route('favorite.toggle', $product->id) }}" method="POST"
                                          class="favorite-form d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light rounded-2" title="إزالة من المفضلة">
                                            <i class="fa fa-heart" style="color: var(--clr-accent);"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Admin Actions - Only for admins --}}
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    <a href="{{ route('products.edit', $product->id) }}"
                                       class="btn btn-sm btn-light rounded-2" title="تعديل">
                                        <i class="fa fa-pen" style="color: var(--clr-primary);"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light rounded-2" title="حذف"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                            <i class="fa fa-trash" style="color: var(--clr-accent);"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isUser = {{ auth()->check() && auth()->user()->role === 'user' ? 'true' : 'false' }};

    // Only initialize favorite removal for user role
    if (isUser) {
        document.querySelectorAll('.favorite-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const col = form.closest('.col-md-4, .col-sm-6');
                const button = form.querySelector('button');

                // Disable button to prevent double submission
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
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
                    if (data.status === false || data.status === 'removed') {
                        if (col) {
                            col.style.transition = 'opacity .4s';
                            col.style.opacity = '0';
                            setTimeout(() => {
                                col.remove();
                                // Check if no more favorites
                                const remainingCards = document.querySelectorAll('.col-md-4, .col-sm-6');
                                if (remainingCards.length === 0) {
                                    location.reload();
                                }
                            }, 400);
                        }
                    } else {
                        // If there's an error, re-enable the button
                        if (button) {
                            button.disabled = false;
                            button.innerHTML = '<i class="fa fa-heart" style="color: var(--clr-accent);"></i>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Re-enable button on error
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = '<i class="fa fa-heart" style="color: var(--clr-accent);"></i>';
                    }
                    // Fallback to form submission
                    form.submit();
                });
            });
        });
    }
});
</script>
@endpush
@endsection
