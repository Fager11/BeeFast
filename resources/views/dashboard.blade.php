@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">

        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-tachometer-alt me-2"></i> لوحة تحكم الإدارة
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

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
                    <div class="row g-4">

                        <div class="col-md-6 col-lg-3">
                            <div class="card text-center border-0 shadow-sm h-100">
                                <div class="card-body d-flex flex-column">
                                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">إدارة المستخدمين</h5>
                                    <p class="card-text text-muted flex-grow-1">عرض وتعديل وحذف المستخدمين.</p>
                                    <a href="{{ url('/users') }}" class="btn btn-outline-primary w-100 mt-auto">
                                        <i class="fas fa-cog"></i> إدارة
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6 col-lg-3">
                            <div class="card text-center border-0 shadow-sm h-100">
                                <div class="card-body d-flex flex-column">
                                    <i class="fas fa-store fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">إدارة المتاجر</h5>
                                    <p class="card-text text-muted flex-grow-1">عرض وتعديل وحذف المتاجر.</p>
                                    <a href="{{ route('stores.index') }}" class="btn btn-info w-100 text-white mt-auto">
                                        <i class="fas fa-store"></i> المتاجر
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6 col-lg-3">
                            <div class="card text-center border-0 shadow-sm h-100">
                                <div class="card-body d-flex flex-column">
                                    <i class="fas fa-box-open fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">إدارة المنتجات</h5>
                                    <p class="card-text text-muted flex-grow-1">إضافة وتعديل وحذف منتجات المتاجر.</p>
                                    <a href="{{ route('products.allStores') }}" class="btn btn-outline-success w-100 mt-auto">
                                        <i class="fas fa-cog"></i> إدارة
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="card text-center border-0 shadow-sm h-100">
                                <div class="card-body d-flex flex-column">
                                    <i class="fas fa-shopping-cart fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">إدارة الطلبات</h5>
                                    <p class="card-text text-muted flex-grow-1">متابعة الطلبات وتعيين السائقين.</p>
                                    <a href="{{ route('orders.index') }}" class="btn btn-outline-warning w-100 mt-auto">
                                        <i class="fas fa-truck"></i> إدارة
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6 col-lg-3">
                            <div class="card text-center border-0 shadow-sm h-100">
                                <div class="card-body d-flex flex-column">
                                    <i class="fas fa-bell fa-3x text-danger mb-3"></i>
                                    <h5 class="card-title">إشعارات الطلبات</h5>
                                    <p class="card-text text-muted flex-grow-1">عرض أحدث الطلبات الواردة.</p>
                                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-danger w-100 mt-auto">
                                        <i class="fas fa-bell"></i> عرض الإشعارات
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>



@endsection
