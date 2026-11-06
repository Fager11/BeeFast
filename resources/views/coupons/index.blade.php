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

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h4 class="fw-bold mb-4" style="color: var(--clr-deep);">
        <i class="fa fa-ticket ms-2"></i> إدارة أكواد الخصم
    </h4>

    {{-- نموذج إنشاء كود --}}
    <div class="card border-0 shadow mb-4">
        <div class="card-header">
            <i class="fa fa-plus ms-2"></i> إنشاء كود جديد
        </div>
        <div class="card-body">
            <form action="{{ route('coupons.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الكود</label>
                        <input type="text" name="code" class="form-control"
                               style="text-transform:uppercase;"
                               placeholder="مثال: BEE10" value="{{ old('code') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نوع الخصم</label>
                        <select name="type" class="form-select">
                            <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>نسبة مئوية (%)</option>
                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>مبلغ ثابت (ل.س)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">قيمة الخصم</label>
                        <input type="number" step="0.01" min="1" name="value" class="form-control"
                               value="{{ old('value') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحد الأدنى للطلب (ل.س)</label>
                        <input type="number" step="0.01" min="0" name="min_order" class="form-control"
                               value="{{ old('min_order', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">أقصى عدد استخدامات</label>
                        <input type="number" min="1" name="max_uses" class="form-control"
                               placeholder="اتركه فارغاً = بلا حد" value="{{ old('max_uses') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">تاريخ الانتهاء</label>
                        <input type="date" name="expires_at" class="form-control"
                               value="{{ old('expires_at') }}">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fa fa-plus ms-1"></i> إضافة الكود
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- جدول الأكواد --}}
    <div class="card border-0 shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" style="color: var(--clr-text);">
                    <thead style="background: rgba(249,115,22,0.10);">
                        <tr>
                            <th class="text-center py-3">الكود</th>
                            <th class="text-center">الخصم</th>
                            <th class="text-center">الحد الأدنى</th>
                            <th class="text-center">الاستخدام</th>
                            <th class="text-center">الانتهاء</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            @php
                                $expired = $coupon->expires_at && $coupon->expires_at->lt(today());
                                $used_up = $coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses;
                            @endphp
                            <tr>
                                <td class="text-center fw-bold">{{ $coupon->code }}</td>
                                <td class="text-center">
                                    @if($coupon->type === 'percent')
                                        {{ (float) $coupon->value }}%
                                    @else
                                        {{ number_format($coupon->value, 0) }} ل.س
                                    @endif
                                </td>
                                <td class="text-center">{{ number_format($coupon->min_order, 0) }} ل.س</td>
                                <td class="text-center">
                                    {{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}
                                </td>
                                <td class="text-center">
                                    {{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : 'بلا انتهاء' }}
                                </td>
                                <td class="text-center">
                                    @if($expired)
                                        <span class="badge rounded-pill bg-secondary">منتهي</span>
                                    @elseif($used_up)
                                        <span class="badge rounded-pill bg-secondary">مستنفد</span>
                                    @elseif($coupon->is_active)
                                        <span class="badge rounded-pill" style="background:#22c55e;">مفعّل</span>
                                    @else
                                        <span class="badge rounded-pill" style="background:#ef4444;">معطّل</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <form action="{{ route('coupons.toggle', $coupon->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-secondary"
                                                    title="{{ $coupon->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                <i class="fa {{ $coupon->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('coupons.destroy', $coupon->id) }}" method="POST"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الكود؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="حذف">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4" style="color: var(--clr-muted);">
                                    لا توجد أكواد خصم حتى الآن
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection