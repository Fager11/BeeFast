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

    <h2 class="mb-4 fw-bold" style="color: var(--clr-deep);">
        <i class="fa fa-receipt ms-2"></i> تفاصيل الطلب رقم #{{ $order->id }}
    </h2>

    <div class="card border-0 shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>الطلب رقم #{{ $order->id }}</span>
            <span class="badge rounded-pill px-3 py-2"
                  style="background:
                    @if($order->status=='pending')     #f97316
                    @elseif($order->status=='in_progress') #3b82f6
                    @elseif($order->status=='on_the_way')  #a855f7
                    @elseif($order->status=='delivered')   #22c55e
                    @elseif($order->status=='cancelled')   #ef4444
                    @endif; color:#fff;">
                @if($order->status=='pending')     قيد الانتظار
                @elseif($order->status=='in_progress') قيد التنفيذ
                @elseif($order->status=='on_the_way')  في الطريق
                @elseif($order->status=='delivered')   تم التوصيل
                @elseif($order->status=='cancelled')   ملغى
                @endif
            </span>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>المستخدم:</strong> {{ $order->user->name }}</p>
                    <p><strong>المجموع الجزئي:</strong> ل.س{{ $order->subtotal }}</p>
                    <p><strong>الخصم:</strong> ل.س{{ $order->discount }}</p>
                    <p><strong>رسوم التوصيل:</strong> ل.س{{ $order->delivery_price }}</p>
                    <p class="fw-bold" style="color: var(--clr-primary);">
                        <strong>الإجمالي:</strong> ل.س{{ $order->total }}
                    </p>
                </div>
            </div>

            <p class="fw-bold mb-2">المنتجات:</p>
            <ul>
                @foreach($order->items as $item)
                    <li>{{ $item->product->name }} × {{ $item->quantity }} (ل.س{{ $item->price }})</li>
                @endforeach
            </ul>
        </div>

        <div class="card-footer">
            <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-right ms-1"></i> العودة إلى الطلبات
            </a>
        </div>
    </div>
</div>
@endsection