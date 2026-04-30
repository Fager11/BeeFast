@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center py-5">
    <div class="col-md-6 col-sm-10">
        <div class="card shadow-lg border-0">
            <div class="card-header text-center py-3">
                <h4 class="mb-0" style="color: var(--clr-deep);">
                    <i class="fa fa-user-plus ms-2"></i> إنشاء حساب
                </h4>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- الاسم --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم</label>
                        <input id="name" type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- البريد --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input id="email" type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- العنوان --}}
                    <div class="mb-3">
                        <label for="address" class="form-label">العنوان</label>
                        <input id="address" type="text"
                            class="form-control @error('address') is-invalid @enderror"
                            name="address" value="{{ old('address') }}" required>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- المدينة + الهاتف --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label">المدينة</label>
                            <input id="city" type="text"
                                class="form-control @error('city') is-invalid @enderror"
                                name="city" value="{{ old('city') }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">الهاتف</label>
                            <input id="phone" type="text"
                                class="form-control @error('phone') is-invalid @enderror"
                                name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- كلمة المرور --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- تأكيد كلمة المرور --}}
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">تأكيد كلمة المرور</label>
                        <input id="password-confirm" type="password" class="form-control"
                            name="password_confirmation" required autocomplete="new-password">
                    </div>

                    {{-- نوع المستخدم --}}
                    <div class="mb-4">
                        <label for="role" class="form-label">نوع المستخدم</label>
                        <select id="role" name="role"
                            class="form-select @error('role') is-invalid @enderror" required>
                            <option value="user">زبون</option>
                            <option value="driver">سائق</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            إنشاء حساب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection