<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('custom-css/index.css') }}">
    <!-- Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @yield('styles')
    @stack('styles')

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}">

    <style>
       
        :root {
            --clr-primary: #f97316;
            
            --clr-deep: #c2410c;
           
            --clr-light: #ffedd5;
           
            --clr-accent: #ef4444;
           
            --clr-warm-white: #fff8f0;
           
            --clr-text: #431407;
           
            --clr-muted: #9a3412;
            
        }

        
        body {
            background-image: url('{{ asset('img/background.png') }}');
            background-size: 50%;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
        }

       
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(160deg,
                    rgba(255, 237, 213, 0.82) 0%,
                    rgba(255, 248, 240, 0.78) 50%,
                    rgba(254, 215, 170, 0.82) 100%);
            z-index: 0;
        }

        #app {
            position: relative;
            z-index: 1;
        }

        
        .navbar {
            background: rgba(255, 248, 240, 0.90) !important;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 2px solid rgba(249, 115, 22, 0.25);
            box-shadow: 0 2px 20px rgba(194, 65, 12, 0.12) !important;
        }

        
        .btn-orange {
            background-color: rgba(249, 115, 22, 0.15);
            border: 1px solid rgba(249, 115, 22, 0.35);
            color: var(--clr-deep) !important;
            font-weight: 700;
            border-radius: 8px;
            padding: 7px 16px;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .btn-orange:hover {
            background-color: var(--clr-primary);
            color: #fff !important;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.35);
        }

       
        .custom-btn-orange {
            background: linear-gradient(135deg, var(--clr-primary), var(--clr-accent));
            color: #fff !important;
            font-weight: 700;
            border-radius: 8px;
            padding: 7px 16px;
            border: none;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .custom-btn-orange:hover {
            background: linear-gradient(135deg, var(--clr-accent), var(--clr-deep));
            transform: scale(1.05);
            box-shadow: 0 4px 14px rgba(249, 115, 22, 0.4);
            color: #fff !important;
        }

       
        .search-input {
            border-radius: 10px;
            padding: 8px 14px;
            border: 2px solid rgba(249, 115, 22, 0.4);
            background: rgba(255, 255, 255, 0.75);
            color: var(--clr-text);
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .search-input:focus {
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.18);
            background: #fff;
            outline: none;
        }

        .search-input::placeholder {
            color: var(--clr-muted);
            opacity: 0.7;
        }

        .btn-transparent-search {
            background: transparent;
            border: none;
            color: var(--clr-primary);
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .btn-transparent-search:hover {
            color: var(--clr-deep);
            transform: scale(1.2);
        }

        
        .btn-light.position-relative {
            background: rgba(255, 255, 255, 0.6) !important;
            border: 1px solid rgba(249, 115, 22, 0.2) !important;
            border-radius: 10px !important;
            transition: all 0.25s;
            color: var(--clr-deep);
        }

        .btn-light.position-relative:hover {
            background: rgba(249, 115, 22, 0.12) !important;
            border-color: rgba(249, 115, 22, 0.4) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
        }

        
        .nav-link.dropdown-toggle {
            color: var(--clr-deep) !important;
            font-weight: 700;
        }

        .nav-link.dropdown-toggle:hover {
            color: var(--clr-primary) !important;
        }

        
        .dropdown-menu {
            background: var(--clr-warm-white) !important;
            border: 1px solid rgba(249, 115, 22, 0.2) !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 30px rgba(194, 65, 12, 0.15) !important;
            padding: 6px !important;
        }

        .dropdown-item {
            color: var(--clr-text) !important;
            border-radius: 8px;
            padding: 0.55rem 1rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background: rgba(249, 115, 22, 0.10) !important;
            color: var(--clr-primary) !important;
        }

       
        .card {
            background: rgba(255, 248, 240, 0.80) !important;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(249, 115, 22, 0.18) !important;
            border-radius: 16px !important;
            box-shadow: 0 6px 30px rgba(194, 65, 12, 0.10) !important;
        }

        .card-header {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.12), rgba(239, 68, 68, 0.08)) !important;
            border-bottom: 1px solid rgba(249, 115, 22, 0.2) !important;
            font-weight: 700;
            color: var(--clr-deep) !important;
        }

        
        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.75) !important;
            border: 1px solid rgba(249, 115, 22, 0.25) !important;
            border-radius: 10px !important;
            color: var(--clr-text) !important;
            transition: all 0.25s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--clr-primary) !important;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.18) !important;
            background: #fff !important;
        }

        
        .btn-primary {
            background: linear-gradient(135deg, var(--clr-primary), var(--clr-accent)) !important;
            border: none !important;
            color: #fff !important;
            font-weight: 700;
            border-radius: 10px !important;
            transition: all 0.25s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.40) !important;
        }

        
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--clr-light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--clr-primary);
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md shadow-sm">
            <div class="container">

                
                <a class="navbar-brand ms-auto" href="{{ url('/') }}">
                    <img src="{{ asset('img/logo.jpg') }}" style="width: 3rem;" />
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    
                    <ul class="navbar-nav ms-auto">
                        @auth
                            @php $role = auth()->user()->role; @endphp
                            <li class="nav-item d-flex">
                                <a class="btn custom-btn-orange align-self-center mx-1"
                                    href="
                                {{ $role == 'admin' ? route('admin.dashboard') : ($role == 'driver' ? route('driver.dashboard') : route('home')) }}
                            ">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    {{ $role == 'admin' ? 'لوحة التحكم' : ($role == 'driver' ? 'لوحة التحكم' : 'الرئيسية') }}
                                </a>
                            </li>
                        @endauth

                        <li class="nav-item d-flex">
                            <a class="btn btn-orange fw-bold mx-1 d-flex align-items-center align-self-center"
                                href="{{ route('stores.index', ['user_mode' => 1]) }}">
                                <i class="bi bi-shop me-2"></i> المتاجر
                            </a>
                        </li>
                    </ul>

                    
                    <div class="d-flex mx-2">
                        <form class="d-flex align-items-center" role="search"
                            action="{{ route('stores.index', ['user_mode' => 1]) }}" method="GET">
                            <input class="form-control search-input" name="search_word" type="search"
                                placeholder="ابحث عن متجر..." value="{{ request('search_word') }}" />
                            <button class="btn btn-transparent-search ms-1" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>

                    
                    <ul class="navbar-nav me-auto align-items-center">

                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item d-flex">
                                    <a class="btn custom-btn-orange mx-1" href="{{ route('login') }}">
                                        تسجيل الدخول
                                    </a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item d-flex">
                                    <a class="btn btn-orange mx-1" href="{{ route('register') }}">
                                        إنشاء حساب
                                    </a>
                                </li>
                            @endif
                        @else
                            
                            <li class="nav-item d-flex">
                                <a href="{{ route('cart.index') }}" class="btn btn-light position-relative m-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                        fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
                                        <path
                                            d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l.5 2H5V5zM6 5v2h2V5zm3 0v2h2V5zm3 0v2h1.36l.5-2zm1.11 3H12v2h.61zM11 8H9v2h2zM8 8H6v2h2zM5 8H3.89l.5 2H5zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0" />
                                    </svg>
                                    @if ($cart_count > 0)
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $cart_count }}
                                        </span>
                                    @endif
                                </a>
                            </li>

                            
                            @php $user = auth()->user(); @endphp
                            @if ($user && $user->role === 'user')
                                <li class="nav-item d-flex">
                                    <a href="{{ route('orders.myOrders') }}" class="btn btn-light position-relative m-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                            fill="currentColor" class="bi bi-basket-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M2.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607L6.29 12H9.71l2.279-8.393L12.39 2.607 12.5 2h1a.5.5 0 0 0 0-1h-11zM5.5 3h5l-1.5 5.5H7L5.5 3zM1 14a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1H1v1z" />
                                        </svg>
                                        @php $ordersCount = auth()->user()->orders()->count(); @endphp
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $ordersCount }}
                                        </span>
                                    </a>
                                </li>
                            @endif

                           
                            <li class="nav-item d-flex">
                                <a href="{{ route('favorites.index') }}" class="btn btn-light position-relative m-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                        fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314" />
                                    </svg>
                                    @if ($favoritesCount > 0)
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $favoritesCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>

                            
                            <li class="nav-item dropdown d-flex">
                                <a class="btn btn-light position-relative m-1" href="{{ route('notifications.index') }}"
                                    id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                        fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901" />
                                    </svg>
                                    @php
                                        $userNotifications = auth()->user()->notifications;
                                        $unreadCount = $userNotifications->whereNull('read_at')->count();
                                    @endphp
                                    @if ($unreadCount > 0)
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown"
                                    style="width:350px;">
                                    @forelse($userNotifications->sortByDesc('created_at')->take(5) as $notification)
                                        @php
                                            $message = $notification->data['message'] ?? 'لديك إشعار جديد';
                                            $orderId = $notification->data['order_id'] ?? null;
                                        @endphp
                                        <li>
                                            <a href="{{ route('notifications.index') }}"
                                                class="dropdown-item d-flex align-items-start
                                               {{ $notification->read_at ? '' : 'fw-bold' }}">
                                                <div>
                                                    <p class="mb-0">{{ $message }}</p>
                                                    @if ($orderId)
                                                        <small style="color: var(--clr-muted);">
                                                            طلب رقم #{{ $orderId }}
                                                        </small><br>
                                                    @endif
                                                    <small style="color: var(--clr-muted);">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="dropdown-item text-center" style="color: var(--clr-muted);">
                                            لا توجد إشعارات
                                        </li>
                                    @endforelse
                                    <li>
                                        <hr class="dropdown-divider" style="border-color: rgba(249,115,22,0.15);">
                                    </li>
                                    <li>
                                        <a href="{{ route('notifications.index') }}"
                                            class="dropdown-item text-center fw-bold" style="color: var(--clr-primary);">
                                            عرض جميع الإشعارات
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            
                            <li class="nav-item dropdown d-flex me-2">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle align-self-center" href="#"
                                    role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    v-pre>
                                    {{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="fa fa-user ms-2"></i> الملف الشخصي
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        <i class="fa fa-arrow-right-from-bracket ms-2"></i> تسجيل الخروج
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>

                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Leaflet Map -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    @yield('scripts')
</body>

</html>
