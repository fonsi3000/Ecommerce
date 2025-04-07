@extends('layouts.app')

@section('title', $producto->name)

@section('content')
<style>
  .product-page {
    --accent-color: #ff4d94;
    --accent-hover: #e03078;
    --light-accent: #fff1f6;
    --text-color: #4a4a4a;
    --heading-font: 'Playfair Display', serif;
    --body-font: 'Nunito', sans-serif;
    font-family: var(--body-font);
    color: var(--text-color);
  }
  /* Estilos generales */
  .product-page {
    --accent-color: #ff4d94;
    --accent-hover: #e03078;
    --light-accent: #fff1f6;
    --text-color: #4a4a4a;
    --heading-font: 'Playfair Display', serif;
    --body-font: 'Nunito', sans-serif;
    
    font-family: var(--body-font);
    color: var(--text-color);
  }
  
  /* Estilos para la sección principal del producto */
  .product-container {
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    position: relative;
  }
  
  /* Galería de imágenes */
  .gallery-container {
    position: relative;
    background-color: #fafafa;
    padding: 20px;
    border-radius: 12px;
  }
  
  .thumbnail-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 15px;
  }
  
  .thumbnail {
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
  }
  
  .thumbnail.active {
    border-color: var(--accent-color);
  }
  
  .thumbnail:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(255, 77, 148, 0.15);
  }
  
  .thumbnail img {
    width: 100%;
    height: 80px;
    object-fit: cover;
    display: block;
  }
  
  .main-image {
    width: 100%;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    position: relative;
  }
  
  .main-image img {
    width: 100%;
    height: auto;
    max-height: 500px;
    object-fit: contain;
    background-color: white;
    display: block;
  }
  
  /* Información del producto */
  .product-info {
    padding: 20px;
  }
  
  .product-title {
    font-family: var(--heading-font);
    color: var(--accent-color);
    font-size: 2.2rem;
    margin-bottom: 15px;
    line-height: 1.2;
    position: relative;
    padding-bottom: 10px;
  }
  
  .product-title:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 80px;
    height: 3px;
    background: linear-gradient(to right, var(--accent-color), transparent);
  }
  
  .product-description {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #666;
    margin-bottom: 25px;
  }
  
  .product-price {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--accent-color);
    margin-bottom: 25px;
    display: flex;
    align-items: center;
  }
  
  .product-price .currency {
    font-size: 1.5rem;
    margin-right: 5px;
    font-weight: 500;
  }
  
  /* Atributos del producto */
  .attributes-container {
    margin-bottom: 25px;
  }
  
  .attributes-title {
    font-weight: 600;
    color: #555;
    margin-bottom: 10px;
    font-size: 1.1rem;
  }
  
  .attributes-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .attribute-tag {
    background-color: var(--light-accent);
    color: var(--accent-color);
    padding: 6px 15px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
  }
  
  .attribute-tag:hover {
    background-color: var(--accent-color);
    color: white;
    transform: translateY(-2px);
  }
  
  /* Stock */
  .stock-info {
    font-weight: 600;
    margin-bottom: 25px;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
  }
  
  .stock-available {
    color: #38b2ac;
  }
  
  .stock-unavailable {
    color: #e53e3e;
  }
  
  .stock-info svg {
    margin-right: 8px;
  }
  
  /* Botón agregar al carrito */
  .cart-button {
    background-color: var(--accent-color);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    box-shadow: 0 5px 15px rgba(255, 77, 148, 0.3);
  }
  
  .cart-button:hover {
    background-color: var(--accent-hover);
    box-shadow: 0 8px 20px rgba(255, 77, 148, 0.4);
    transform: translateY(-2px);
  }
  
  .cart-button svg {
    margin-right: 10px;
  }
  
  /* Productos relacionados */
  .related-products {
    margin-top: 80px;
    position: relative;
    padding-bottom: 50px;
  }
  
  .related-products:before {
    content: '';
    position: absolute;
    top: -40px;
    left: 50%;
    transform: translateX(-50%);
    width: 200px;
    height: 1px;
    background: linear-gradient(to right, transparent, var(--accent-color), transparent);
  }
  
  .related-title {
    text-align: center;
    font-family: var(--heading-font);
    color: var(--accent-color);
    font-size: 2rem;
    margin-bottom: 30px;
    position: relative;
  }
  
  .related-title-emoji {
    margin: 0 10px;
    display: inline-block;
    animation: pulse 2s infinite;
  }
  
  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
  }
  
  .slider-container {
    position: relative;
    overflow: hidden;
    padding: 0 30px;
  }
  
  .slider-track {
    display: flex;
    transition: transform 0.5s ease-in-out;
  }
  
  .slider-item {
    flex: 0 0 100%;
    padding: 0 10px;
  }
  
  @media (min-width: 640px) {
    .slider-item {
      flex: 0 0 50%;
    }
  }
  
  @media (min-width: 768px) {
    .slider-item {
      flex: 0 0 33.333%;
    }
  }
  
  @media (min-width: 1024px) {
    .slider-item {
      flex: 0 0 25%;
    }
  }
  
  .slider-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background-color: white;
    border-radius: 50%;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s ease;
  }
  
  .slider-arrow:hover {
    background-color: var(--accent-color);
    color: white;
  }
  
  .slider-arrow.prev {
    left: 0;
  }
  
  .slider-arrow.next {
    right: 0;
  }
  
  .pagination-dots {
    display: flex;
    justify-content: center;
    margin-top: 20px;
  }
  
  .pagination-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #ddd;
    margin: 0 5px;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .pagination-dot.active {
    background-color: var(--accent-color);
    transform: scale(1.3);
  }
  
  /* Responsive mejoras */
  @media (max-width: 768px) {
    .product-title {
      font-size: 1.8rem;
    }
    
    .product-price {
      font-size: 1.8rem;
    }
    
    .thumbnail img {
      height: 60px;
    }
    
    .main-image img {
      max-height: 400px;
    }
    
    .cart-button {
      padding: 12px 25px;
    }
  }
  
  @media (max-width: 640px) {
    .thumbnail-grid {
      grid-template-columns: repeat(4, 1fr);
    }
    
    .product-title {
      font-size: 1.6rem;
    }
    
    .product-description {
      font-size: 1rem;
    }
    
    .related-title {
      font-size: 1.6rem;
    }
    
    .main-image img {
      max-height: 350px;
    }
  }

  /* Se mantienen todos los estilos de tu versión anterior */
</style>

<div class="product-page">
  <section class="max-w-7xl mx-auto py-10 px-4" x-data="{
      active: 0,
      currentAttribute: null,
      setAttribute(attrId) {
        this.currentAttribute = attrId;
        this.active = 0;
      },
      isVisibleImage(index, attrId) {
        return this.currentAttribute === null || this.currentAttribute === attrId;
      }
    }">
    <div class="product-container">
      <div class="grid md:grid-cols-2 gap-8 items-start">
        <!-- Galería -->
        <div class="gallery-container">
          <div class="thumbnail-grid">
            <!-- Imagen principal -->
            <div class="thumbnail" :class="{ 'active': active === 0 }" @click="active = 0" x-show="currentAttribute === null">
              <img src="{{ asset('storage/' . $producto->image) }}" alt="{{ $producto->name }}">
            </div>
            <!-- Miniaturas con atributo -->
            @foreach ($producto->images as $index => $img)
              <div class="thumbnail"
                   x-show="isVisibleImage({{ $index + 1 }}, {{ $img->attribute_id ?? 'null' }})"
                   :class="{ 'active': active === {{ $index + 1 }} }"
                   @click="active = {{ $index + 1 }}">
                <img src="{{ asset('storage/' . $img->image_path) }}" alt="Miniatura">
              </div>
            @endforeach
          </div>

          <div class="main-image">
            <template x-if="active === 0 && currentAttribute === null">
              <img src="{{ asset('storage/' . $producto->image) }}" alt="{{ $producto->name }}">
            </template>
            @foreach ($producto->images as $index => $img)
              <template x-if="active === {{ $index + 1 }}">
                <img src="{{ asset('storage/' . $img->image_path) }}" alt="Imagen relacionada">
              </template>
            @endforeach
          </div>
        </div>

        <!-- Información -->
        <div class="product-info">
          <h1 class="product-title">{{ $producto->name }}</h1>
          <p class="product-description">{{ $producto->description }}</p>
          <div class="product-price">
            <span class="currency">$</span>
            {{ number_format($producto->price, 0, ',', '.') }}
          </div>

          <!-- Atributos -->
          @if ($atributos->count())
            <div class="attributes-container">
              <h3 class="attributes-title">Tonos disponibles:</h3>
              <div class="attributes-list">
                <span class="attribute-tag" @click="setAttribute(null)">Todos</span>
                @foreach ($atributos as $attr)
                  <span class="attribute-tag" @click="setAttribute({{ $attr->id }})">
                    {{ $attr->name }}
                    @php
                      $stock = $producto->attributes->firstWhere('id', $attr->id)?->pivot->stock;
                    @endphp
                    ({{ $stock ?? 0 }} disponibles)
                  </span>
                @endforeach
              </div>
            </div>
          @endif

          <!-- Stock general -->
          @if ($producto->stock > 0)
            <div class="stock-info stock-available">
              ✅ Disponible: {{ $producto->stock }} unidades
            </div>
          @else
            <div class="stock-info stock-unavailable">
              ❌ Agotado
            </div>
          @endif

          <!-- Botón agregar -->
          <button class="cart-button">
            🛒 Agregar al carrito
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- Productos relacionados -->
  <section class="related-products max-w-7xl mx-auto px-4">
    <h2 class="related-title">
      <span class="related-title-emoji">💖</span>
      Productos que te pueden interesar
      <span class="related-title-emoji">💖</span>
    </h2>
    <div class="slider-container" x-data="{
        currentSlide: 0,
        slidesCount: {{ ceil($relacionados->count() / 4) }},
        next() { this.currentSlide = (this.currentSlide + 1) % this.slidesCount },
        prev() { this.currentSlide = (this.currentSlide - 1 + this.slidesCount) % this.slidesCount },
        init() { setInterval(() => this.next(), 5000) }
    }">
      <template x-if="slidesCount > 1">
        <div>
          <button class="slider-arrow prev" @click="prev">←</button>
          <button class="slider-arrow next" @click="next">→</button>
        </div>
      </template>

      <div class="slider-track" :style="'transform: translateX(-' + (currentSlide * 100) + '%)'">
        @foreach ($relacionados as $r)
          <div class="slider-item">
            @include('components.product-card', ['producto' => $r])
          </div>
        @endforeach
      </div>

      <template x-if="slidesCount > 1">
        <div class="pagination-dots">
          <template x-for="i in slidesCount" :key="i">
            <span class="pagination-dot" :class="{ 'active': currentSlide === i - 1 }" @click="currentSlide = i - 1"></span>
          </template>
        </div>
      </template>
    </div>
  </section>
</div>
@endsection
