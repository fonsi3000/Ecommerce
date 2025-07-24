@extends('layouts.app')

@section('title', 'Resultados de búsqueda: ' . $query)

@section('content')
<style>
  /* Estilos específicos para la página de búsqueda */
  .search-page {
    --accent-color: #ff4d94;
    --accent-hover: #e03078;
    --light-accent: #fff1f6;
    --text-color: #4a4a4a;
    --heading-font: 'Playfair Display', serif;
    --body-font: 'Nunito', sans-serif;
    
    font-family: var(--body-font);
    color: var(--text-color);
  }
  
  /* Cabecera de la página */
  .search-header {
    text-align: center;
    padding-bottom: 20px;
    position: relative;
    margin-bottom: 30px;
  }
  
  .search-header:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 200px;
    height: 1px;
    background: linear-gradient(to right, transparent, var(--accent-color), transparent);
  }
  
  .search-title {
    font-family: var(--heading-font);
    color: var(--accent-color);
    font-size: 2.5rem;
    margin-bottom: 10px;
    line-height: 1.2;
  }
  
  .search-subtitle {
    font-size: 1.2rem;
    color: #666;
  }
  
  .search-query {
    color: var(--accent-color);
    font-weight: 600;
  }
  
  /* Formulario de búsqueda */
  .search-form {
    max-width: 700px;
    margin: 0 auto 40px;
  }
  
  .search-input {
    border-radius: 50px 0 0 50px;
    border: 2px solid var(--light-accent);
    border-right: none;
    padding: 15px 25px;
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(255, 77, 148, 0.1);
    transition: all 0.3s ease;
  }
  
  .search-input:focus {
    outline: none;
    border-color: var(--accent-color);
    box-shadow: 0 4px 20px rgba(255, 77, 148, 0.2);
  }
  
  .search-button {
    background-color: var(--accent-color);
    color: white;
    border: 2px solid var(--accent-color);
    border-radius: 0 50px 50px 0;
    padding: 15px 30px;
    font-size: 1.1rem;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  
  .search-button:hover {
    background-color: var(--accent-hover);
    border-color: var(--accent-hover);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 77, 148, 0.3);
  }
  
  /* Contenedor de resultados */
  .results-container {
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    padding: 30px;
    margin-bottom: 40px;
    position: relative;
  }
  
  .results-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
  }
  
  .results-count {
    font-size: 1.1rem;
    color: #666;
  }
  
  .results-count span {
    color: var(--accent-color);
    font-weight: 600;
  }
  
  /* Filtros */
  .sort-dropdown {
    position: relative;
  }
  
  .sort-button {
    background-color: white;
    border: 1px solid #e0e0e0;
    border-radius: 30px;
    padding: 8px 20px;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
  }
  
  .sort-button:hover {
    border-color: var(--accent-color);
    color: var(--accent-color);
  }
  
  .sort-menu {
    position: absolute;
    right: 0;
    top: 110%;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    width: 220px;
    z-index: 10;
    overflow: hidden;
  }
  
  .sort-option {
    padding: 12px 20px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    color: #555;
    display: flex;
    align-items: center;
  }
  
  .sort-option:hover {
    background-color: var(--light-accent);
    color: var(--accent-color);
  }
  
  /* Cuadrícula de productos */
  .products-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
  
  @media (min-width: 640px) {
    .products-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 25px;
    }
  }
  
  @media (min-width: 768px) {
    .products-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  
  @media (min-width: 1024px) {
    .products-grid {
      grid-template-columns: repeat(4, 1fr);
      gap: 30px;
    }
  }
  
  /* Mensajes sin resultados */
  .no-results-container {
    text-align: center;
    padding: 50px 20px;
    border-radius: 16px;
    background-color: var(--light-accent);
    margin-bottom: 40px;
  }
  
  .no-results-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 20px;
    color: var(--accent-color);
    opacity: 0.7;
  }
  
  .no-results-title {
    font-family: var(--heading-font);
    color: var(--accent-color);
    font-size: 1.8rem;
    margin-bottom: 10px;
  }
  
  .no-results-message {
    color: #666;
    max-width: 500px;
    margin: 0 auto 20px;
    line-height: 1.6;
  }
  
  .back-button {
    display: inline-block;
    padding: 10px 25px;
    background-color: white;
    color: var(--accent-color);
    border: 2px solid var(--accent-color);
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  
  .back-button:hover {
    background-color: var(--accent-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 77, 148, 0.2);
  }
  
  /* Categorías destacadas */
  .categories-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 25px;
    max-width: 1000px;
    margin: 30px auto 0;
  }
  
  @media (min-width: 640px) {
    .categories-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  @media (min-width: 992px) {
    .categories-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }
  
  .category-card {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 100%;
    border: 1px solid #f0f0f0;
  }
  
  .category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(255, 77, 148, 0.15);
    border-color: var(--light-accent);
  }
  
  .category-image {
    position: relative;
    height: 180px;
    overflow: hidden;
    background-color: #f9f9f9;
  }
  
  .category-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }
  
  .category-card:hover .category-image img {
    transform: scale(1.05);
  }
  
  .category-info {
    padding: 20px;
    text-align: center;
  }
  
  .category-name {
    font-family: var(--heading-font);
    font-size: 1.3rem;
    color: var(--text-color);
    margin-bottom: 5px;
    transition: color 0.3s ease;
  }
  
  .category-card:hover .category-name {
    color: var(--accent-color);
  }
  
  .category-count {
    font-size: 0.9rem;
    color: #888;
  }
  
  /* Decoraciones */
  .search-decoration {
    position: absolute;
    opacity: 0.1;
    z-index: -1;
  }
  
  .decoration-1 {
    top: 5%;
    left: 5%;
    width: 120px;
    height: 120px;
    border-radius: 60% 40% 70% 30% / 40% 50% 50% 60%;
    background-color: var(--accent-color);
    animation: floatAnimation 8s ease-in-out infinite alternate;
  }
  
  .decoration-2 {
    bottom: 10%;
    right: 5%;
    width: 150px;
    height: 150px;
    border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
    background-color: var(--accent-color);
    animation: floatAnimation 10s ease-in-out infinite alternate-reverse;
  }
  
  @keyframes floatAnimation {
    0% {
      transform: translate(0, 0) rotate(0deg);
    }
    100% {
      transform: translate(15px, 15px) rotate(10deg);
    }
  }
  
  /* Paginación */
  .pagination-container {
    margin-top: 35px;
    display: flex;
    justify-content: center;
  }
</style>

<div class="search-page relative py-10 px-4 sm:px-6 lg:px-8">
  <!-- Decoraciones de fondo -->
  <div class="search-decoration decoration-1"></div>
  <div class="search-decoration decoration-2"></div>
  
  <div class="max-w-7xl mx-auto">
    <!-- Cabecera de búsqueda -->
    <div class="search-header">
      <h1 class="search-title">Buscar Productos</h1>
      
      @if($query)
        <p class="search-subtitle">
          Resultados para: <span class="search-query">"{{ $query }}"</span>
        </p>
      @endif
    </div>
    
    <!-- Formulario de búsqueda -->
    <div class="search-form">
      <form action="{{ route('productos.buscar') }}" method="GET" class="flex">
        <input 
          type="text" 
          name="q" 
          value="{{ $query }}" 
          placeholder="¿Qué estás buscando?" 
          class="search-input flex-1 focus:outline-none"
        >
        <button 
          type="submit" 
          class="search-button"
        >
          Buscar
        </button>
      </form>
    </div>
    
    <!-- Cuando hay resultados -->
    @if($productos->isNotEmpty())
      <div class="results-container">
        <!-- Cabecera de resultados -->
        <div class="results-header">
          <p class="results-count">
            <span>{{ $productos->total() }}</span> productos encontrados
          </p>
          
          <!-- Opciones de ordenación -->
          <div class="sort-dropdown" x-data="{ open: false }">
            <button 
              @click="open = !open" 
              class="sort-button"
            >
              <span>Ordenar por</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            
            <div 
              x-show="open" 
              @click.outside="open = false"
              x-transition
              class="sort-menu"
            >
              <a href="{{ route('productos.buscar', ['q' => $query, 'sort' => 'precio_asc']) }}" class="sort-option">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
                Precio: menor a mayor
              </a>
              <a href="{{ route('productos.buscar', ['q' => $query, 'sort' => 'precio_desc']) }}" class="sort-option">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                Precio: mayor a menor
              </a>
              <a href="{{ route('productos.buscar', ['q' => $query, 'sort' => 'nombre_asc']) }}" class="sort-option">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9M3 12h5"></path>
                </svg>
                Nombre: A-Z
              </a>
              <a href="{{ route('productos.buscar', ['q' => $query, 'sort' => 'nombre_desc']) }}" class="sort-option">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9M3 12h5"></path>
                </svg>
                Nombre: Z-A
              </a>
            </div>
          </div>
        </div>
        
        <!-- Cuadrícula de productoss -->
        <div class="products-grid">
          @foreach($productos as $producto)
            @include('components.product-card', ['producto' => $producto])
          @endforeach
        </div>
        
        {{-- Botón Ver más --}}
            <div class="mt-10 flex justify-center">
                {{ $productos->links('components.pagination-custom') }}
            </div>
      </div>
    @endif
    
    <!-- Cuando no hay resultados -->
    @if($productos->isEmpty() && $query)
      <div class="no-results-container">
        <svg class="no-results-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h2 class="no-results-title">No encontramos resultados</h2>
        <p class="no-results-message">
          No encontramos productos que coincidan con "<span class="font-semibold">{{ $query }}</span>". 
          Intenta con otras palabras o revisa nuestras categorías.
        </p>
        
        <a href="{{ route('inicio') }}" class="back-button">
          Volver al inicio
        </a>
      </div>
    @endif
    
    <!-- Cuando no hay query (página inicial) -->
    @if(!$query)
      <div class="text-center py-8">
        <svg class="w-20 h-20 text-pink-400 mx-auto mb-6 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-3 font-serif">¿Qué estás buscando hoy?</h2>
        <p class="text-gray-600 max-w-md mx-auto mb-8 text-lg">
          Encuentra los productos perfectos para ti navegando por categorías o usando nuestro buscador.
        </p>
        
        <h3 class="text-xl font-serif text-pink-600 mb-6 mt-10">Explora por categorías</h3>
        <!-- Categorías destacadas -->
        <div class="categories-grid">
          @foreach($menuCategories as $category)
            <a href="{{ route('categoria.show', $category->slug) }}" class="category-card">
              <div class="category-image">
                @if($category->image)
                  <img 
                    src="{{ asset('storage/' . $category->image) }}" 
                    alt="{{ $category->name }}"
                  >
                @else
                  <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                @endif
              </div>
              <div class="category-info">
                <h3 class="category-name">{{ $category->name }}</h3>
                @if($category->subCategories->count() > 0)
                  <p class="category-count">{{ $category->subCategories->count() }} subcategorías</p>
                @endif
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>
@endsection