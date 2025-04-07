<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Mi Tienda')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css') {{-- Tailwind CSS --}}
</head>
<body class="bg-white text-gray-800">

    {{-- Header reutilizable --}}
    @include('layouts.header')

    {{-- Contenido principal --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-100 py-6 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} Luis carrascal. Todos los derechos reservados.
    </footer>

    {{-- Scripts empujados dinámicamente --}}
    @stack('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>

</body>
</html>
