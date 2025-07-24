@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    {{-- Slider --}}
    <x-slider :slides="[
        ['image' => asset('images/slider1.png'), 'text' => ''],
        // ['image' => asset('images/slider2.png'), 'text' => 'Transforma tu look con nuestras marcas exclusivas ¡Hasta 40% OFF!'],
        // ['image' => asset('images/slider3.png'), 'text' => '¡Solo por 3 días! Compra 2 productos y lleva el tercero GRATIS'],
    ]" />

    {{-- Productos destacados --}}
    <section class="bg-white py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold text-pink-600 mb-8 text-center">
                💖 Nuestros Productos 💖
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($productos as $producto)
                    <x-product-card :producto="$producto" />
                @empty
                    <p class="text-center col-span-4 text-gray-500">No hay productos disponibles</p>
                @endforelse

                
            </div>

            {{-- Botón Ver más --}}
                <div class="mt-10 flex justify-center">
                    {{ $productos->links('components.pagination-custom') }}
                </div>

            
        </div>
    </section>
@endsection
