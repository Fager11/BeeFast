@extends('layouts.app')

@section('styles')
<style>
body {
    background-color: #FFF5E6;
    font-family: 'Nunito', sans-serif;
}


.card-product {
    background-color:rgb(243, 202, 156); 
    border-radius: 25px;
    padding: 20px 30px;
    max-width: 450px;
    margin: 40px auto;
    box-shadow: 0 3px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}
.card-product:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

h1 {
    color: #FF8C42;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
}

label {
    font-weight: bold;
    color: #FF8C42;
}


.form-control, .form-select {
    border-radius: 20px;
    padding: 10px 15px;
    transition: all 0.3s;
    border: 1px solid #FFCC99;
}
.form-control:focus, .form-select:focus {
    border-color: #FF8C42;
    box-shadow: 0 0 6px rgba(255,140,66,0.3);
}


button, .btn-secondary {
    padding: 10px 25px;
    border-radius: 20px;
    font-weight: bold;
    transition: all 0.3s ease;
}
.btn-orange {
    background-color: #FFB97B;
    color: #fff;
    border: none;
}
.btn-orange:hover {
    background-color: #FF8C42;
    transform: scale(1.05);
}
.btn-secondary {
    background-color: #D9CAB3;
    color: #333;
}
.btn-secondary:hover {
    background-color: #C7B08F;
    transform: scale(1.05);
}


.alert {
    border-radius: 12px;
    font-weight: 500;
}


.img-thumbnail {
    border-radius: 15px;
}
</style>
@endsection

@section('content')
<div class="container" dir="rtl">

    {{-- رسائل النجاح --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2" style="font-size:1.2rem;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- رسائل الخطأ --}}
    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-x-circle-fill me-2" style="font-size:1.2rem;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="card card-product">
        <h1>تعديل المنتج</h1>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- اسم المنتج --}}
            <div class="mb-3">
                <label for="name">اسم المنتج</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $product->name) }}">
            </div>

  
            <div class="mb-3">
                <label for="quantity">الكمية</label>
                <input type="number" name="quantity" class="form-control" required min="0" value="{{ old('quantity', $product->quantity ?? 0) }}">
            </div>

        
            <div class="mb-3">
                <label for="description">الوصف</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
            </div>

     
            <div class="mb-3">
                <label for="price">السعر</label>
                <input type="number" step="0.01" name="price" class="form-control" required value="{{ old('price', $product->price) }}">
            </div>

          
            <div class="mb-3">
                <label for="store_id">المتجر</label>
                <select name="store_id" class="form-select" required>
                    <option value="">اختر المتجر</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ old('store_id', $product->store_id) == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
            </div>

       
            <div class="mb-3">
                <label for="image">صورة المنتج</label>
                @if($product->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-height:120px;">
                    </div>
                @endif
                <input type="file" name="image" class="form-control">
            </div>

           
            <div class="d-flex justify-content-center gap-3 mt-4">
                <button type="submit" class="btn btn-orange">
                    <i class="bi bi-pencil-square"></i> تعديل المنتج
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>

        </form>
    </div>
</div>
@endsection
