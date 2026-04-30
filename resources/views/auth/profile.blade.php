@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h1 class="text-center mb-4" style="color: var(--clr-deep);">
        <i class="fa fa-user ms-2"></i> الملف الشخصي
    </h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header">
                    <i class="fa fa-pen ms-2"></i> تعديل البيانات
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('update_profile') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">الاسم</label>
                                <input id="name" type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') ?? $user->name }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') ?? $user->email }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="address" class="form-label">العنوان</label>
                                <input id="address" type="text"
                                    class="form-control @error('address') is-invalid @enderror"
                                    name="address" value="{{ old('address') ?? $user->address }}" required>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="city" class="form-label">المدينة</label>
                                <input id="city" type="text"
                                    class="form-control @error('city') is-invalid @enderror"
                                    name="city" value="{{ old('city') ?? $user->city }}" required>
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="phone" class="form-label">الهاتف</label>
                                <input id="phone" type="text"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    name="phone" value="{{ old('phone') ?? $user->phone }}" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">كلمة المرور الجديدة</label>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password" autocomplete="new-password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6" id="confirm-wrapper" style="display: {{ old('password') ? 'block' : 'none' }}">
                                <label for="password-confirm" class="form-label">تأكيد كلمة المرور</label>
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation">
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fa fa-save ms-2"></i> حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const password = document.getElementById('password');
    const confirmDiv = document.getElementById('confirm-wrapper');
    password.addEventListener('input', function () {
        confirmDiv.style.display = password.value.trim() !== '' ? 'block' : 'none';
    });
});
</script>
@endsection