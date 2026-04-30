@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: var(--clr-deep);">
            <i class="fa fa-bell ms-2"></i> الإشعارات
        </h2>
        <button id="clear-notifications" class="btn btn-sm btn-danger">
            <i class="fa fa-trash ms-1"></i> حذف الكل
        </button>
    </div>

    @php
        $notifications = auth()->user()->notifications()->latest()->get();
        $statusArabic  = [
            'pending'     => 'قيد الانتظار',
            'in_progress' => 'قيد التنفيذ',
            'on_the_way'  => 'في الطريق',
            'delivered'   => 'تم التسليم',
            'cancelled'   => 'ملغى',
        ];
        $statusStyles = [
            'pending'     => ['badge'=>'#f97316', 'icon'=>'fa-hourglass-half'],
            'in_progress' => ['badge'=>'#3b82f6', 'icon'=>'fa-gear'],
            'on_the_way'  => ['badge'=>'#a855f7', 'icon'=>'fa-truck'],
            'delivered'   => ['badge'=>'#22c55e', 'icon'=>'fa-circle-check'],
            'cancelled'   => ['badge'=>'#ef4444', 'icon'=>'fa-circle-xmark'],
        ];
    @endphp

    @if($notifications->isEmpty())
        <div class="text-center py-5" style="color: var(--clr-muted);">
            <i class="fa fa-bell-slash fa-4x mb-3 d-block" style="opacity:.3;"></i>
            <p class="fs-5">لا توجد إشعارات</p>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($notifications as $notification)
                @php
                    $message    = $notification->data['message']  ?? 'لديك إشعار جديد';
                    $orderId    = $notification->data['order_id'] ?? null;
                    $status     = $notification->data['status']   ?? null;
                    $badgeColor = $statusStyles[$status]['badge'] ?? '#999';
                    $icon       = $statusStyles[$status]['icon']  ?? 'fa-bell';
                    $statusText = $statusArabic[$status]          ?? $status;
                    $isUnread   = !$notification->read_at;
                @endphp

                <div class="card border-0 shadow-sm notification-card"
                     style="background: rgba(255,248,240,0.85);
                            {{ $isUnread ? 'border-right: 4px solid var(--clr-primary) !important;' : '' }}">
                    <div class="card-body d-flex justify-content-between align-items-start gap-3">
                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:46px; height:46px;
                                        background: {{ $badgeColor }}22;">
                                <i class="fa {{ $icon }}" style="color: {{ $badgeColor }}; font-size:1.2rem;"></i>
                            </div>
                            <div>
                                @if($orderId)
                                    <p class="mb-1 fw-bold" style="color: var(--clr-deep);">
                                        طلب رقم #{{ $orderId }}
                                        <span class="badge rounded-pill ms-2 px-2"
                                              style="background: {{ $badgeColor }}; color:#fff; font-size:.75rem;">
                                            {{ $statusText }}
                                        </span>
                                    </p>
                                @endif
                                <p class="mb-1" style="color: var(--clr-text);">{{ $message }}</p>
                                <small style="color: var(--clr-muted);">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>

                        @if($isUnread)
                            <button class="btn btn-sm mark-read-btn flex-shrink-0"
                                    style="background: rgba(249,115,22,0.1); color: var(--clr-primary);
                                           border: 1px solid rgba(249,115,22,0.3); border-radius:8px;"
                                    data-id="{{ $notification->id }}">
                                <i class="fa fa-check ms-1"></i> مقروء
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .notification-card { border-radius:12px !important; transition:transform .2s, box-shadow .2s; }
    .notification-card:hover { transform:translateY(-3px); box-shadow:0 6px 20px rgba(194,65,12,.12) !important; }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.mark-read-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id   = this.dataset.id;
            const card = this.closest('.card');
            fetch(`/notifications/${id}/mark-read`, {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    card.style.borderRight = 'none';
                    this.remove();
                }
            });
        });
    });

    document.getElementById('clear-notifications').addEventListener('click', function () {
        if (!confirm('هل أنت متأكد أنك تريد حذف جميع الإشعارات؟')) return;
        fetch('{{ route('notifications.clear') }}', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Accept':'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-card').forEach(c => c.remove());
            }
        });
    });
});
</script>
@endsection
@endsection