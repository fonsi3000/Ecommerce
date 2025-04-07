@extends('layouts.app')

@section('title', $categoria->name)

@section('content')
<style>
  /* Estilos generales femeninos */
  .category-header {
    background: linear-gradient(to right, #fff1f6, #ffe8f3, #ffd1ec);
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 40px;
    box-shadow: 0 8px 20px rgba(255, 77, 148, 0.08);
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  
  .category-header:before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 150px;
    height: 150px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath fill='%23ff4d94' fill-opacity='0.08' d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z'/%3E%3C/svg%3E");
    opacity: 0.5;
  }
  
  .category-title {
    font-family: 'Playfair Display', serif;
    color: #ff4d94;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    position: relative;
    display: inline-block;
  }
  
  .category-title:after {
    content: '';
    display: block;
    width: 50%;
    height: 3px;
    background: linear-gradient(to right, #ff4d94, transparent);
    margin: 5px auto 0;
  }
  
  .category-subtitle {
    color: #a239ca;
    font-size: 1.1rem;
    font-weight: 400;
    font-style: italic;
    margin-top: 10px;
    opacity: 0.8;
  }
  
  .products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
  }
  
  @media (min-width: 768px) {
    .products-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  
  @media (min-width: 1024px) {
    .products-grid {
      grid-template-columns: repeat(4, 1fr);
    }
  }
  
  .empty-message {
    text-align: center;
    padding: 40px;
    background-color: #fff8fa;
    border-radius: 12px;
    color: #ff4d94;
    font-size: 1.1rem;
    border: 1px dashed #ffcce0;
  }
  
  .empty-message svg {
    margin-bottom: 15px;
    opacity: 0.6;
  }
  
  /* Animación sutil para las cards al cargar la página */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .products-grid > * {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
  }
  
  .products-grid > *:nth-child(1) { animation-delay: 0.1s; }
  .products-grid > *:nth-child(2) { animation-delay: 0.2s; }
  .products-grid > *:nth-child(3) { animation-delay: 0.3s; }
  .products-grid > *:nth-child(4) { animation-delay: 0.4s; }
  .products-grid > *:nth-child(5) { animation-delay: 0.5s; }
  .products-grid > *:nth-child(6) { animation-delay: 0.6s; }
  .products-grid > *:nth-child(7) { animation-delay: 0.7s; }
  .products-grid > *:nth-child(8) { animation-delay: 0.8s; }
</style>

<div class="max-w-7xl mx-auto py-10 px-4">
    <div class="category-header">
        <h1 class="category-title">{{ $categoria->name }}</h1>
    </div>

    @if ($productos->count())
        <div class="products-grid">
            @foreach ($productos as $producto)
                @include('components.product-card', ['producto' => $producto])
            @endforeach
        </div>
    @else
        <div class="empty-message">
            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#ff4d94" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                <line x1="7" y1="7" x2="7.01" y2="7"></line>
            </svg>
            <p>No hay productos disponibles en esta categoría.</p>
            <p class="mt-2 text-sm">Vuelve pronto para ver nuestras novedades.</p>
        </div>
    @endif
</div>
@endsection