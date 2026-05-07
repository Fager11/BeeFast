@extends('layouts.app')

@section('content')
<div class="container mt-4">

    
    <div class="text-center mb-4">
        <img src="{{ asset('img/logo.jpg') }}" alt="logo" class="img-fluid" style="max-width:200px;">
    </div>

    
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2" style="font-size:1.2rem;"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-x-circle-fill me-2" style="font-size:1.2rem;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    
    @if(isset($noResults) && $noResults)
        <div class="alert alert-warning d-flex align-items-center mt-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2" style="font-size:1.2rem;"></i>
            <div>لم يتم العثور على أي متجر باسم "{{ request('search_word') }}"</div>
        </div>
    @endif

    
    <div class="row mb-4">
        <h3 class="mb-3">ماذا تريد أن تطلب؟</h3>
        @php
            $categories = [
                ['type'=>'restaurant','name'=>'مطاعم','icon'=>'bi-shop'],
                ['type'=>'store','name'=>'متاجر','icon'=>'bi-basket2-fill'],
                ['type'=>'cafe','name'=>'مقاهي','icon'=>'bi-cup-straw'],
            ];
        @endphp

        @foreach($categories as $cat)
            <div class="col-md-4 mb-3">
                <a href="{{ route('stores.index', ['type'=>$cat['type'], 'user_mode'=>1]) }}" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100 card-hover">
                        <div class="d-flex justify-content-center align-items-center mt-3">
                            <i class="bi {{ $cat['icon'] }} display-1 bg-primary text-white p-4 rounded-circle"></i>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title">{{ $cat['name'] }}</h4>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    
    @foreach(['stores'=>'المتاجر الاكثر رواجا', 'restaurants'=>'المطاعم الاكثر رواجا', 'cafes'=>'المقاهي الاكثر رواجا'] as $var=>$title)
        <div class="row mb-4">
            <h3 class="mb-3">{{ $title }}</h3>
            @forelse ($$var as $item)
                <div class="col-md-3 mb-3">
                    <a href="{{ route('stores.show', ['store'=>$item->id]) }}" class="text-decoration-none">
                        <div class="card shadow-sm h-100 card-hover">
                           <img src="{{ asset('storage/'.$item->image) }}" class="card-img-top" alt="صورة {{ $item->name }}" style="height:200px; object-fit:cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $item->name }}</h5>
                                <p class="text-muted mb-2">قيمة التوصيل: {{ $item->delivery_price }} ل.س</p>
                                <span class="badge bg-info-subtle">{{ $item->type }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-secondary text-center">
                        لا يوجد {{ strtolower($title) }} حتى الآن
                    </div>
                </div>
            @endforelse
        </div>
    @endforeach

</div>

<style>
.card-hover:hover {
    transform: translateY(-5px) scale(1.02);
    transition: all 0.3s;
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}
</style>
@endsection
