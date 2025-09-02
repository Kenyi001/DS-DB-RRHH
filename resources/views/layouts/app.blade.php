<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema RRHH YPFB') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Header -->
    <nav class="bg-ypfb-blue border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and title -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <img class="h-8 w-auto" src="/images/ypfb-logo.png" alt="YPFB" onerror="this.style.display='none'">
                    </div>
                    <div class="ml-4">
                        <h1 class="text-xl font-semibold text-white">
                            Sistema RRHH YPFB-Andina
                        </h1>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="hidden md:flex md:items-center md:space-x-4">
                    <a href="{{ route('empleados.index') }}" 
                       class="text-white hover:text-ypfb-light px-3 py-2 rounded-md text-sm font-medium">
                        Empleados
                    </a>
                    <a href="#" 
                       class="text-white hover:text-ypfb-light px-3 py-2 rounded-md text-sm font-medium">
                        Contratos
                    </a>
                    <a href="#" 
                       class="text-white hover:text-ypfb-light px-3 py-2 rounded-md text-sm font-medium">
                        Planilla
                    </a>
                    
                    <!-- User menu -->
                    <div class="ml-4 flex items-center">
                        <span class="text-white text-sm mr-3">{{ auth()->user()?->name ?? 'Usuario' }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-white text-ypfb-blue px-3 py-1 rounded text-sm hover:bg-gray-100">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-sm text-gray-500">
                © 2025 YPFB-Andina. Sistema de Recursos Humanos - Versión 1.0
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>