<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Custom CSS to override Tailwind conflicts -->
        <style>
            /* إعادة تعيين بعض خصائص Tailwind التي تتعارض مع Bootstrap */
            .container {
                max-width: 100% !important;
            }
            
            .table {
                width: 100% !important;
                margin-bottom: 1rem !important;
                color: #212529 !important;
                background-color: transparent !important;
            }
            
            .table th,
            .table td {
                padding: 0.75rem !important;
                vertical-align: top !important;
                border-top: 1px solid #dee2e6 !important;
            }
            
            .table thead th {
                vertical-align: bottom !important;
                border-bottom: 2px solid #dee2e6 !important;
            }
            
            .btn {
                display: inline-block !important;
                font-weight: 400 !important;
                text-align: center !important;
                vertical-align: middle !important;
                user-select: none !important;
                border: 1px solid transparent !important;
                padding: 0.375rem 0.75rem !important;
                font-size: 1rem !important;
                line-height: 1.5 !important;
                border-radius: 0.25rem !important;
                transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
            }
            
            .card {
                position: relative !important;
                display: flex !important;
                flex-direction: column !important;
                min-width: 0 !important;
                word-wrap: break-word !important;
                background-color: #fff !important;
                background-clip: border-box !important;
                border: 1px solid rgba(0, 0, 0, 0.125) !important;
                border-radius: 0.25rem !important;
            }
            
            .card-body {
                flex: 1 1 auto !important;
                padding: 1rem !important;
            }
            
            .alert {
                position: relative !important;
                padding: 0.75rem 1.25rem !important;
                margin-bottom: 1rem !important;
                border: 1px solid transparent !important;
                border-radius: 0.25rem !important;
            }
            
            .alert-success {
                color: #155724 !important;
                background-color: #d4edda !important;
                border-color: #c3e6cb !important;
            }
            
            .form-control {
                display: block !important;
                width: 100% !important;
                padding: 0.375rem 0.75rem !important;
                font-size: 1rem !important;
                font-weight: 400 !important;
                line-height: 1.5 !important;
                color: #495057 !important;
                background-color: #fff !important;
                background-clip: padding-box !important;
                border: 1px solid #ced4da !important;
                border-radius: 0.25rem !important;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
            }
            
            .form-select {
                display: block !important;
                width: 100% !important;
                padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;
                font-size: 1rem !important;
                font-weight: 400 !important;
                line-height: 1.5 !important;
                color: #495057 !important;
                background-color: #fff !important;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e") !important;
                background-repeat: no-repeat !important;
                background-position: right 0.75rem center !important;
                background-size: 16px 12px !important;
                border: 1px solid #ced4da !important;
                border-radius: 0.25rem !important;
                appearance: none !important;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
        
        <!-- Stack Scripts -->
        @stack('scripts')
    </body>
</html>
