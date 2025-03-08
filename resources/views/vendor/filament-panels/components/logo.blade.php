@php
    $brandName = filament()->getBrandName();
    $brandLogo = asset('images/logo.png'); // Asegúrate de que esta ruta sea correcta
    $brandLogoHeight = '4rem'; // Ajusta esto según el tamaño que desees
@endphp

<img
    alt="{{ $brandName }}"
    src="{{ $brandLogo }}"
    style="height: {{ $brandLogoHeight }};"
    class="fi-logo"
/> 
{{-- aqui --}}