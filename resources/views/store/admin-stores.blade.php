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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold" style="color: var(--clr-deep);">
            <i class="fa fa-store ms-2"></i> إدارة المتاجر
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('stores.create') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-plus ms-1"></i> متجر جديد
            </a>
            <a href="{{ route('stores.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-right ms-1"></i> رجوع
            </a>
        </div>
    </div>

    <div class="card border-0 shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="color: var(--clr-text);">
                    <thead style="background: rgba(249,115,22,0.10);">
                        <tr>
                            <th class="text-center py-3">#</th>
                            <th class="text-center">الاسم</th>
                            <th class="text-center">الصورة</th>
                            <th class="text-center">سعر التوصيل</th>
                            <th class="text-center">النوع</th>
                            <th class="text-center">رائج</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stores as $store)
                            <tr>
                                <th class="text-center align-middle">{{ $store->id }}</th>
                                <td class="text-center align-middle fw-bold">{{ $store->name }}</td>
                                <td class="text-center align-middle">
                                    <img src="{{ asset('storage/' . $store->image) }}"
                                         alt="صورة المتجر"
                                         class="rounded-3 shadow-sm"
                                         style="width:70px; height:70px; object-fit:cover;">
                                </td>
                                <td class="text-center align-middle">{{ $store->delivery_price }} ل.س</td>
                                <td class="text-center align-middle">
                                    @switch($store->type)
                                        @case('restaurant') مطعم @break
                                        @case('cafe')       مقهى @break
                                        @default            متجر
                                    @endswitch
                                </td>
                                <td class="text-center align-middle">
                                    @if($store->popular)
                                        <span class="badge rounded-pill" style="background: rgba(249,115,22,0.2); color: var(--clr-deep);">رائج</span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-muted">غير رائج</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('stores.show', ['store'=>$store->id]) }}"
                                           class="btn btn-sm btn-outline-secondary" title="عرض">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('stores.edit', ['store'=>$store->id]) }}"
                                           class="btn btn-sm btn-primary" title="تعديل">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                        <form method="POST" action="{{ route('stores.destroy', $store->id) }}"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا المتجر؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4" style="color: var(--clr-muted);">
                                    لا يوجد متاجر حتى الآن
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $stores->links() }}
    </div>
</div>
@endsection