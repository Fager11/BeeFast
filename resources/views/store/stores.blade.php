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

    {{-- Admin Actions - Only visible to admin users --}}
    @if(auth()->check() && auth()->user()->role === 'admin')
        <div class="mb-4 text-end">
            <a href="{{ route('stores.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa fa-plus ms-2"></i> إضافة متجر جديد
            </a>
        </div>
    @endif

    <div class="row g-3">
        @forelse ($stores as $store)
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 border-0 shadow-sm store-card">
                    @if($store->image)
                        <img src="{{ asset('storage/' . $store->image) }}"
                             class="card-img-top"
                             alt="{{ 'صورة متجر ' . $store->name }}"
                             style="height: 180px; object-fit: cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center"
                             style="height: 180px; background: rgba(249,115,22,0.08);">
                            <i class="fa fa-store fa-3x" style="color: var(--clr-primary); opacity: 0.5;"></i>
                        </div>
                    @endif

                    <div class="card-body">
                        <a href="{{ route('stores.show', ['store' => $store->id]) }}" class="text-decoration-none">
                            <h5 class="fw-bold mb-1" style="color: var(--clr-deep);">{{ $store->name }}</h5>
                        </a>
                        <p class="mb-1 small" style="color: var(--clr-muted);">
                            <i class="fa fa-motorcycle ms-1"></i>
                            سعر التوصيل: {{ $store->delivery_price }} ل.س
                        </p>
                        <span class="badge rounded-pill mt-1"
                              style="background: rgba(249,115,22,0.15); color: var(--clr-deep);">
                            @switch($store->type)
                                @case('restaurant') مطعم @break
                                @case('cafe')       مقهى @break
                                @default            متجر
                            @endswitch
                        </span>
                    </div>

                    {{-- Admin actions for each store - Only visible to admin users --}}
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <div class="card-footer border-0 bg-transparent d-flex justify-content-between gap-2">
                            <a href="{{ route('stores.edit', $store->id) }}"
                               class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="fa fa-pen ms-1"></i> تعديل
                            </a>
                            <form action="{{ route('stores.destroy', $store->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger w-100"
                                        onclick="return confirm('هل أنت متأكد من حذف هذا المتجر؟');">
                                    <i class="fa fa-trash ms-1"></i> حذف
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5" style="color: var(--clr-muted);">
                <i class="fa fa-store fa-3x mb-3 d-block" style="opacity:.4"></i>
                لا يوجد متاجر حتى الآن
            </div>
        @endforelse
    </div>
</div>

<style>
    .store-card {
        transition: transform .25s ease, box-shadow .25s ease;
        border-radius: 14px !important;
        overflow: hidden;
    }
    .store-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 25px rgba(194,65,12,.18) !important;
    }
    .store-card .card-img-top {
        transition: transform .4s ease;
    }
    .store-card:hover .card-img-top {
        transform: scale(1.05);
    }
    .card-footer {
        padding-top: 0;
    }
</style>
@endsection
