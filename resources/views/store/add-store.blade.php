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

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow border-0">
                <div class="card-header">
                    <i class="fa fa-plus ms-2"></i> إنشاء متجر جديد
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('stores.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">اسم المتجر</label>
                            <input id="name" type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">الصورة</label>
                            <input id="image" type="file"
                                class="form-control @error('image') border-danger @enderror"
                                name="image" required>
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="delivery_price" class="form-label">سعر التوصيل (ل.س)</label>
                            <input id="delivery_price" type="number"
                                class="form-control @error('delivery_price') is-invalid @enderror"
                                name="delivery_price" value="{{ old('delivery_price') }}" required>
                            @error('delivery_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نوع المتجر</label>
                            <select class="form-select" name="type">
                                <option value="store"      {{ old('type','store') == 'store'      ? 'selected' : '' }}>متجر</option>
                                <option value="restaurant" {{ old('type') == 'restaurant'         ? 'selected' : '' }}>مطعم</option>
                                <option value="cafe"       {{ old('type') == 'cafe'               ? 'selected' : '' }}>مقهى</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="popular" value="1"
                                    id="popular" {{ old('popular') == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="popular">رائج</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fa fa-plus ms-2"></i> إضافة
                            </button>
                            <a href="{{ route('stores.index') }}" class="btn btn-secondary px-4">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection