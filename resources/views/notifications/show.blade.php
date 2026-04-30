@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">إدارة الطلبات</h2>

    <div class="row">
        @forelse($orders as $order)
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>طلب رقم #{{ $order->id }}</span>
                    <span class="badge 
                        @if($order->status=='pending') bg-warning
                        @elseif($order->status=='in_progress') bg-primary
                        @elseif($order->status=='on_the_way') bg-info
                        @elseif($order->status=='delivered') bg-success
                        @elseif($order->status=='cancelled') bg-danger
                        @endif">
                        @if($order->status=='pending') قيد الانتظار
                        @elseif($order->status=='in_progress') جارٍ المعالجة
                        @elseif($order->status=='on_the_way') في الطريق
                        @elseif($order->status=='delivered') تم التوصيل
                        @elseif($order->status=='cancelled') ملغى
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <p><strong>المستخدم:</strong> {{ $order->user->name }}</p>
                    <p><strong>المجموع الفرعي:</strong> ${{ $order->subtotal }}</p>
                    <p><strong>الخصم:</strong> ${{ $order->discount }}</p>
                    <p><strong>رسوم التوصيل:</strong> ${{ $order->delivery_price }}</p>
                    <p><strong>الإجمالي:</strong> ${{ $order->total }}</p>
                    <p><strong>المنتجات:</strong></p>
                    <ul>
                        @foreach($order->items as $item)
                            <li>{{ $item->product->name }} × {{ $item->quantity }} (${{ $item->price }})</li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer">
                    @if(auth()->user()->role == 'admin' && $order->status == 'pending')
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="status" value="in_progress">
                        <button class="btn btn-success btn-sm">قبول الطلب</button>
                    </form>
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button class="btn btn-danger btn-sm">رفض الطلب</button>
                    </form>

                    <form action="{{ route('orders.assignDriver', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        <select name="driver_id" class="form-select d-inline-block w-auto">
                            <option value="">اختر السائق</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary btn-sm">تعيين السائق</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <p class="text-center">لا توجد طلبات حالياً.</p>
        @endforelse
    </div>
</div>
@endsection
