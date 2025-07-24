@extends('layouts.app')

@section('title', $producto->name)

@section('content')
{{-- Definir $totalStock al inicio de la vista para que esté disponible en toda la plantilla --}}
@php
  $totalStock = $producto->variants->sum('stock');
  $colorVariants = $producto->variants->where('atributo_tipo', 'color');
  $tonoVariants = $producto->variants->where('atributo_tipo', 'tono');
@endphp
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
    cursor: pointer;
  }
  
  .main-image img {
    width: 100%;
    height: auto;
    max-height: 500px;
    object-fit: contain;
    background-color: white;
    display: block;
  }
  
  /* Lightbox para imágenes */
  .lightbox {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
  }
  
  .lightbox.active {
    opacity: 1;
    pointer-events: auto;
  }
  
  .lightbox-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
  }
  
  .lightbox-image {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain;
    display: block;
    margin: 0 auto;
  }
  
  .lightbox-close {
    position: absolute;
    top: -40px;
    right: 0;
    background: none;
    border: none;
    color: white;
    font-size: 30px;
    cursor: pointer;
  }
  
  .lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 24px;
    color: white;
    border: none;
    transition: background-color 0.3s;
  }
  
  .lightbox-nav:hover {
    background-color: rgba(255, 255, 255, 0.4);
  }
  
  .lightbox-prev {
    left: 20px;
  }
  
  .lightbox-next {
    right: 20px;
  }
  
  /* Contador de imágenes en lightbox */
  .lightbox-counter {
    position: absolute;
    bottom: -30px;
    left: 0;
    right: 0;
    color: white;
    text-align: center;
    font-size: 14px;
  }
  
  /* Navegación de imágenes en miniatura */
  .gallery-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 30px;
    height: 30px;
    background-color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 5;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: none;
    font-size: 18px;
    color: var(--accent-color);
    transition: all 0.3s;
  }
  
  .gallery-nav:hover {
    background-color: var(--accent-color);
    color: white;
  }
  
  .gallery-prev {
    left: 5px;
  }
  
  .gallery-next {
    right: 5px;
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
    cursor: pointer;
  }
  
  .attribute-tag:hover {
    background-color: var(--accent-color);
    color: white;
    transform: translateY(-2px);
  }
  
  .attribute-tag.active {
    background-color: var(--accent-color);
    color: white;
  }
  
  /* Colores y Tonos */
  .color-swatch {
    display: inline-block;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    margin-right: 5px;
    border: 1px solid #ddd;
    vertical-align: middle;
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

  /* Estilos para el selector de cantidad */
  .custom-number-input input:focus {
    outline: none !important;
  }

  .custom-number-input button:focus {
    outline: none !important;
  }

  .custom-number-input input[type=number]::-webkit-inner-spin-button,
  .custom-number-input input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .custom-number-input input[type=number] {
    -moz-appearance: textfield;
  }

  /* Botón deshabilitado */
  .cart-button:disabled {
    background-color: #cccccc;
    box-shadow: none;
    cursor: not-allowed;
  }
</style>

<div class="product-page">
  <section class="max-w-7xl mx-auto py-10 px-4" x-data="{
    active: 0,
    selectedVariant: null,
    quantity: 1,
    variantStock: 0,
    showLightbox: false,
    lightboxIndex: 0,
    // Crear un array con todas las imágenes
    images: [
      '{{ asset('storage/' . $producto->image) }}',
      @foreach($producto->images as $img)
        '{{ asset('storage/' . $img->image_path) }}',
      @endforeach
    ],
    // Array de variantes para acceder a sus propiedades
    variants: [
      @foreach($producto->variants as $variant)
        {
          id: {{ $variant->id }},
          tipo: '{{ $variant->atributo_tipo }}',
          nombre: '{{ $variant->atributo_tipo == 'color' ? $variant->nombre_color : $variant->nombre_tono }}',
          stock: {{ $variant->stock }}
        },
      @endforeach
    ],
    // Métodos para la galería
    updateSelectedVariant(el) {
      // Quitar clase active de todos los elementos
      document.querySelectorAll('.attribute-tag').forEach(tag => {
        tag.classList.remove('active');
      });
      
      // Añadir clase active al elemento clickeado
      if (el) {
        el.classList.add('active');
      }
      
      // Actualizar el stock de la variante seleccionada
      if (this.selectedVariant === null) {
        this.variantStock = {{ $totalStock }};
      } else {
        const variant = this.variants.find(v => v.id === this.selectedVariant);
        this.variantStock = variant ? variant.stock : 0;
      }
      
      // Ajustar la cantidad si es mayor que el stock disponible
      if (this.quantity > this.variantStock) {
        this.quantity = this.variantStock > 0 ? this.variantStock : 1;
      }
    },

    addToCart() {
  console.log('Intentando agregar al carrito');
  
  // Validar stock antes de agregar
  if ((this.selectedVariant && this.variantStock <= 0) || 
      (!this.selectedVariant && {{ $totalStock <= 0 ? 'true' : 'false' }})) {
    return;
  }
  
  // Obtenemos la información del producto
  const variantName = this.selectedVariant ? this.variants.find(v => v.id === this.selectedVariant).nombre : null;
  
  // Creamos el objeto del producto para agregar al carrito
  const producto = {
    id: {{ $producto->id }},
    name: '{{ $producto->name }}' + (variantName ? ` - ${variantName}` : ''),
    price: {{ $producto->price }},
    image: '{{ $producto->image }}',
    quantity: this.quantity,
    variant_id: this.selectedVariant
  };
  
  console.log('Producto a agregar:', producto);
  
  // Usar Alpine directamente para agregar al carrito y abrir el modal
  if (typeof Alpine !== 'undefined' && Alpine.store) {
    let cartStore = Alpine.store('cart');
    cartStore.addItem(producto);
    
    // Forzar la apertura del modal directamente
    cartStore.isOpen = true;
    
    // Emitir un evento que pueda ser capturado desde otros componentes
    window.dispatchEvent(new CustomEvent('open-cart-modal'));
    
    // Feedback visual
    const button = this.$el;
    const originalText = button.innerHTML;
    button.innerHTML = '✅ ¡Añadido!';
    
    setTimeout(() => {
      button.innerHTML = originalText;
    }, 2000);
  } else {
    console.error('El store del carrito no está disponible');
  }
},
    nextImage() {
      this.active = (this.active + 1) % this.images.length;
    },
    prevImage() {
      this.active = (this.active - 1 + this.images.length) % this.images.length;
    },
    openLightbox(index) {
      this.lightboxIndex = index;
      this.showLightbox = true;
      document.body.style.overflow = 'hidden';
    },
    closeLightbox() {
      this.showLightbox = false;
      document.body.style.overflow = '';
    },
    nextLightboxImage() {
      this.lightboxIndex = (this.lightboxIndex + 1) % this.images.length;
    },
    prevLightboxImage() {
      this.lightboxIndex = (this.lightboxIndex - 1 + this.images.length) % this.images.length;
    },
    init() {
      // Inicializar el stock total
      this.variantStock = {{ $totalStock }};
    }
  }">
    <div class="product-container">
      <div class="grid md:grid-cols-2 gap-8 items-start">
        <!-- Galería -->
        <div class="gallery-container">
          <div class="thumbnail-grid">
            <!-- Imagen principal -->
            <div class="thumbnail" :class="{ 'active': active === 0 }" @click="active = 0">
              <img src="{{ asset('storage/' . $producto->image) }}" alt="{{ $producto->name }}">
            </div>
            
            <!-- Todas las imágenes adicionales -->
            @foreach ($producto->images as $index => $img)
              <div class="thumbnail" :class="{ 'active': active === {{ $index + 1 }} }" @click="active = {{ $index + 1 }}">
                <img src="{{ asset('storage/' . $img->image_path) }}" alt="Imagen {{ $index + 1 }}">
              </div>
            @endforeach
          </div>

          <div class="main-image" @click="openLightbox(active)">
            <!-- Navegación de la galería -->
            <button class="gallery-nav gallery-prev" @click.stop="prevImage()">←</button>
            <button class="gallery-nav gallery-next" @click.stop="nextImage()">→</button>
            
            <!-- Imagen principal mostrada -->
            <template x-for="(src, index) in images" :key="index">
              <img 
                :src="src" 
                :alt="index === 0 ? '{{ $producto->name }}' : 'Imagen ' + index"
                x-show="active === index"
                style="transition: opacity 0.3s ease;">
            </template>
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

          <!-- Colores (Tipo de atributo: color) -->
          @php
            $colorVariants = $producto->variants->where('atributo_tipo', 'color');
          @endphp
          @if ($colorVariants->count() > 0)
            <div class="attributes-container">
              <h3 class="attributes-title">Colores disponibles:</h3>
              <div class="attributes-list">
                <span class="attribute-tag" 
                      :class="{ 'active': selectedVariant === null }" 
                      @click="selectedVariant = null; updateSelectedVariant($event.currentTarget)">
                  Todos
                </span>
                @foreach ($colorVariants as $variant)
                  <span class="attribute-tag" 
                        :class="{ 'active': selectedVariant === {{ $variant->id }} }"
                        @click="selectedVariant = {{ $variant->id }}; updateSelectedVariant($event.currentTarget)">
                    <span class="color-swatch" style="background-color: {{ $variant->color }};"></span>
                    {{ $variant->nombre_color }}
                    ({{ $variant->stock }} disponibles)
                  </span>
                @endforeach
              </div>
            </div>
          @endif

          <!-- Tonos (Tipo de atributo: tono) -->
          @php
            $tonoVariants = $producto->variants->where('atributo_tipo', 'tono');
          @endphp
          @if ($tonoVariants->count() > 0)
            <div class="attributes-container">
              <h3 class="attributes-title">Tonos disponibles:</h3>
              <div class="attributes-list">
                <span class="attribute-tag" 
                      :class="{ 'active': selectedVariant === null }" 
                      @click="selectedVariant = null; updateSelectedVariant($event.currentTarget)">
                  Todos
                </span>
                @foreach ($tonoVariants as $variant)
                  <span class="attribute-tag" 
                        :class="{ 'active': selectedVariant === {{ $variant->id }} }"
                        @click="selectedVariant = {{ $variant->id }}; updateSelectedVariant($event.currentTarget)">
                    <span class="color-swatch" style="background-color: {{ $variant->tono }};"></span>
                    {{ $variant->nombre_tono }}
                    ({{ $variant->stock }} disponibles)
                  </span>
                @endforeach
              </div>
            </div>
          @endif

          <!-- Stock general -->
          @php
            $totalStock = $producto->variants->sum('stock');
          @endphp
          
          <div class="stock-info" :class="selectedVariant ? 'stock-available' : '{{ $totalStock > 0 ? 'stock-available' : 'stock-unavailable' }}'">
            <template x-if="selectedVariant && variantStock > 0">
              <span>✅ Disponible: <span x-text="variantStock"></span> unidades</span>
            </template>
            <template x-if="selectedVariant && variantStock <= 0">
              <span>❌ Variante agotada</span>
            </template>
            <template x-if="!selectedVariant">
              @if ($totalStock > 0)
                <span>✅ Disponible: {{ $totalStock }} unidades en total</span>
              @else
                <span>❌ Agotado</span>
              @endif
            </template>
          </div>

          <!-- Selector de cantidad -->
          <div class="flex items-center mb-6 mt-4">
            <span class="text-gray-700 font-medium mr-4">Cantidad:</span>
            <div class="custom-number-input flex h-10 w-32">
              <button @click="quantity > 1 ? quantity-- : null" 
                      class="bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-300 h-full w-10 rounded-l cursor-pointer outline-none flex items-center justify-center">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
              </button>
              <input type="number" 
                     x-model.number="quantity" 
                     min="1" 
                     :max="selectedVariant ? variantStock : {{ $totalStock }}"
                     class="outline-none focus:outline-none text-center w-full bg-gray-100 font-semibold text-md text-gray-700 flex items-center" 
                     name="custom-input-number">
              <button @click="quantity++" 
                      :disabled="selectedVariant ? quantity >= variantStock : quantity >= {{ $totalStock }}"
                      :class="{ 'opacity-50 cursor-not-allowed': selectedVariant ? quantity >= variantStock : quantity >= {{ $totalStock }} }"
                      class="bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-300 h-full w-10 rounded-r cursor-pointer flex items-center justify-center">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Botón agregar -->
          <button class="cart-button" 
                  @click="addToCart()"
                  :disabled="(selectedVariant && variantStock <= 0) || (!selectedVariant && {{ $totalStock <= 0 ? 'true' : 'false' }})"
                  :class="{ 'opacity-50 cursor-not-allowed': (selectedVariant && variantStock <= 0) || (!selectedVariant && {{ $totalStock <= 0 ? 'true' : 'false' }}) }">
            🛒 Agregar al carrito
          </button>
        </div>
      </div>
    </div>
    
    <!-- Lightbox para ver imágenes en grande -->
    <div class="lightbox" :class="{ 'active': showLightbox }" @click="closeLightbox()">
      <div class="lightbox-content" @click.stop>
        <button class="lightbox-close" @click="closeLightbox()">&times;</button>
        
        <template x-for="(src, index) in images" :key="index">
          <img 
            :src="src" 
            :alt="'Imagen en grande ' + index" 
            class="lightbox-image"
            x-show="lightboxIndex === index"
            style="transition: opacity 0.3s ease;">
        </template>
        
        <button class="lightbox-nav lightbox-prev" @click.stop="prevLightboxImage()">←</button>
        <button class="lightbox-nav lightbox-next" @click.stop="nextLightboxImage()">→</button>
        
        <div class="lightbox-counter" x-text="(lightboxIndex + 1) + ' / ' + images.length"></div>
      </div>
    </div>
    </section>

  <!-- Productos relacionados -->
  <section class="related-products max-w-7xl mx-auto px-4">
    <h2 class="related-title">
      <span class="related-title-emoji">💖</span>
      Productos similares
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

<!-- Eliminamos el script anterior para usar el Store de Alpine.js -->
@endsection