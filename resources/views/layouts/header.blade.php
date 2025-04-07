{{-- Alpine.js desde CDN (sin Vite) --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<header x-data="{ 
    mobileMenu: false,
    searchOpen: false,
    searchQuery: '',
    searchResults: [],
    isSearching: false,
    
    // Método para realizar la búsqueda
    performSearch() {
      if (this.searchQuery.length < 2) {
        this.searchResults = [];
        return;
      }
      
      this.isSearching = true;
      
      // Realizar la búsqueda mediante fetch
      fetch(`/api/buscar-productos?query=${encodeURIComponent(this.searchQuery)}`)
        .then(response => response.json())
        .then(data => {
          this.searchResults = data;
          this.isSearching = false;
        })
        .catch(error => {
          console.error('Error en la búsqueda:', error);
          this.isSearching = false;
        });
    },
    
    // Método para limpiar la búsqueda
    clearSearch() {
      this.searchQuery = '';
      this.searchResults = [];
      this.searchOpen = false;
    }
  }" 
  class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 py-2 sm:py-3 flex items-center justify-between">
        {{-- Logo más compacto pero adaptable --}}
        <a href="{{ route('inicio') }}" class="flex items-center space-x-1">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 sm:h-20 md:h-24">
        </a>

        {{-- Menú escritorio y tablet --}}
        <nav class="hidden sm:flex items-center space-x-2 md:space-x-6 font-bold text-sm md:text-base lg:text-lg text-gray-800">
            <a href="{{ route('inicio') }}"
               class="hover:text-pink-600 {{ request()->routeIs('inicio') ? 'text-pink-600 underline' : '' }}">
                INICIO
            </a>

            @foreach ($menuCategories as $category)
                <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="setTimeout(() => open = false, 300)">
                    <a href="{{ route('categoria.show', $category->slug) }}"
                       class="hover:text-pink-600 flex items-center space-x-1 py-1 {{ request()->is('categoria/'.$category->slug) ? 'text-pink-600 underline' : '' }}">
                        <span>{{ strtoupper($category->name) }}</span>
                        @if ($category->subCategories->isNotEmpty())
                            <svg class="w-4 h-4 md:w-5 md:h-5 text-pink-500" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"/>
                            </svg>
                        @endif
                    </a>

                    @if ($category->subCategories->isNotEmpty())
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute top-full left-0 bg-white border border-gray-200 rounded shadow-md z-50 min-w-[160px] sm:min-w-[140px] md:min-w-[180px]"
                             style="margin-top: 1px;">
                            <div class="absolute h-1 w-full -top-1"></div>
                            @foreach ($category->subCategories as $sub)
                                <a href="{{ route('categoria.subcategoria', [
                                    'categoria_slug' => $category->slug,
                                    'subcategoria_slug' => $sub->slug,
                                ]) }}"
                                   class="block px-3 py-2 md:px-4 md:py-3 text-gray-700 hover:bg-pink-50 hover:text-pink-600 text-sm md:text-base 
                                   {{ request()->routeIs('categoria.subcategoria') && request()->route('categoria_slug') == $category->slug && request()->route('subcategoria_slug') == $sub->slug ? 'bg-pink-50 text-pink-600 font-semibold' : '' }}">
                                    {{ $sub->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

        </nav>

        {{-- Íconos y botón menú móvil --}}
        <div class="flex items-center space-x-3 sm:space-x-4 md:space-x-6 text-gray-700">
            {{-- Buscar con dropdown mejorado --}}
            <div class="relative">
                <button @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())" 
                        class="hover:text-pink-600 focus:outline-none transition-colors duration-200 transform hover:scale-110"
                        :class="{'text-pink-600': searchOpen}">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                
                <!-- Dropdown del buscador mejorado -->
                <div x-show="searchOpen" 
                     @click.outside="searchOpen = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-3 w-72 sm:w-80 md:w-96 bg-white rounded-lg shadow-xl border border-pink-100 z-50 overflow-hidden">
                    <div class="p-4">
                        <!-- Campo de búsqueda mejorado -->
                        <div class="flex items-center bg-pink-50 rounded-full px-4 py-2 border border-pink-100 focus-within:ring-2 focus-within:ring-pink-400 focus-within:border-pink-400 transition-all duration-200">
                            <svg class="w-5 h-5 text-pink-400 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input 
                                x-ref="searchInput"
                                type="text" 
                                x-model="searchQuery" 
                                @input="performSearch"
                                @keydown.enter="if(searchResults.length > 0 || searchQuery.length >= 2) window.location.href='/buscar?q='+searchQuery"
                                placeholder="¿Qué estás buscando?" 
                                class="w-full focus:outline-none text-gray-700 bg-transparent placeholder-gray-500">
                            <button 
                                x-show="searchQuery.length > 0" 
                                @click="clearSearch" 
                                class="text-pink-400 hover:text-pink-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Estado de carga mejorado -->
                        <div x-show="isSearching" class="py-6 text-center text-gray-500">
                            <div class="animate-spin h-8 w-8 mx-auto border-3 border-pink-300 border-t-pink-600 rounded-full"></div>
                            <p class="mt-3 text-sm text-gray-600">Buscando productos...</p>
                        </div>
                        
                        <!-- Mensaje de no resultados mejorado -->
                        <div x-show="!isSearching && searchQuery.length >= 2 && searchResults.length === 0" class="py-6 text-center">
                            <svg class="w-10 h-10 text-pink-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-600">No encontramos productos</p>
                            <a href="/buscar?q=all" class="block text-pink-500 hover:underline text-sm mt-2">Ver catálogo completo</a>
                        </div>
                        
                        <!-- Resultados de búsqueda mejorados -->
                        <div x-show="!isSearching && searchResults.length > 0" class="mt-4 max-h-72 overflow-y-auto custom-scrollbar">
                            <h3 class="text-xs uppercase text-gray-500 font-semibold mb-2 px-2">Resultados</h3>
                            <template x-for="producto in searchResults" :key="producto.id">
                                <a :href="'/producto/' + producto.slug" class="block py-3 px-3 hover:bg-pink-50 rounded-md mb-1 transition-colors flex items-center group">
                                    <div class="w-14 h-14 rounded-md overflow-hidden bg-gray-100 border border-gray-200 flex-shrink-0 group-hover:border-pink-200 transition-colors">
                                        <img :src="'/storage/' + producto.image" class="w-full h-full object-cover" :alt="producto.name">
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="font-medium text-gray-800 group-hover:text-pink-600 transition-colors line-clamp-1" x-text="producto.name"></div>
                                        <div class="text-pink-600 font-semibold" x-text="'$' + Number(producto.price).toLocaleString()"></div>
                                    </div>
                                </a>
                            </template>
                            
                            <!-- Ver todos los resultados mejorado -->
                            <a :href="'/buscar?q=' + searchQuery" class="flex items-center justify-center py-3 text-pink-600 hover:text-pink-700 mt-2 border-t border-pink-100 font-medium hover:bg-pink-50 transition-colors">
                                <span>Ver todos los resultados</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Carrito (nuevo icono) --}}
            <a href="#" class="relative hover:text-pink-600 transition-colors duration-200 transform hover:scale-110">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs sm:text-sm px-1.5 sm:px-2 py-0.5 rounded-full font-bold">0</span>
            </a>

            {{-- Botón móvil --}}
            <button @click="mobileMenu = !mobileMenu" class="sm:hidden focus:outline-none">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-pink-600" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Menú móvil --}}
    <div
        x-show="mobileMenu"
        x-transition
        x-cloak
        @click.outside="mobileMenu = false"
        class="sm:hidden bg-white border-t border-gray-100 shadow-md"
    >
        <div class="px-4 py-3 space-y-2 text-gray-800 font-bold text-base">
            {{-- Campo de búsqueda en menú móvil (mejorado) --}}
            <div class="mb-4 relative">
                <div class="flex items-center border border-pink-200 rounded-full overflow-hidden bg-pink-50 px-4 py-3">
                    <svg class="w-5 h-5 text-pink-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input 
                        type="text" 
                        x-model="searchQuery" 
                        @input="performSearch"
                        @keydown.enter="if(searchResults.length > 0 || searchQuery.length >= 2) window.location.href='/buscar?q='+searchQuery"
                        placeholder="¿Qué estás buscando?" 
                        class="w-full bg-transparent focus:outline-none text-gray-700 placeholder-gray-500">
                    <button 
                        x-show="searchQuery.length > 0" 
                        @click="clearSearch" 
                        class="text-pink-400 hover:text-pink-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Resultados de búsqueda en móvil (mejorados) -->
                <div 
                    x-show="searchQuery.length >= 2 && searchResults.length > 0" 
                    x-transition
                    class="absolute left-0 right-0 mt-2 bg-white border border-pink-100 rounded-lg shadow-lg z-50 max-h-72 overflow-y-auto">
                    
                    <!-- Estado de carga móvil -->
                    <div x-show="isSearching" class="py-4 text-center text-gray-500">
                        <div class="animate-spin h-6 w-6 mx-auto border-2 border-pink-300 border-t-pink-600 rounded-full"></div>
                        <p class="mt-2 text-sm">Buscando...</p>
                    </div>
                    
                    <template x-for="producto in searchResults" :key="producto.id">
                        <a :href="'/producto/' + producto.slug" class="flex items-center py-2 px-3 hover:bg-pink-50 border-b border-pink-50 last:border-b-0">
                            <img :src="'/storage/' + producto.image" class="w-12 h-12 object-cover rounded-md border border-gray-200" :alt="producto.name">
                            <div class="ml-3">
                                <div class="font-medium text-gray-800 text-sm" x-text="producto.name"></div>
                                <div class="text-pink-600 text-sm font-semibold" x-text="'$' + Number(producto.price).toLocaleString()"></div>
                            </div>
                        </a>
                    </template>
                    
                    <a :href="'/buscar?q=' + searchQuery" class="block text-center py-3 text-pink-600 hover:bg-pink-50 text-sm font-medium border-t border-pink-100">
                        Ver todos los resultados
                    </a>
                </div>
            </div>
            
            <a href="{{ route('inicio') }}" class="block hover:text-pink-600" @click="mobileMenu = false">INICIO</a>

            @foreach ($menuCategories as $category)
                <div>
                    <a href="{{ route('categoria.show', $category->slug) }}"
                       class="block hover:text-pink-600 {{ request()->is('categoria/'.$category->slug) ? 'text-pink-600 underline' : '' }}"
                       @click="mobileMenu = false">
                        {{ strtoupper($category->name) }}
                    </a>

                    @if ($category->subCategories->isNotEmpty())
                        <div class="pl-4 mt-1 space-y-1">
                            @foreach ($category->subCategories as $sub)
                                <a href="{{ route('categoria.subcategoria', [
                                    'categoria_slug' => $category->slug,
                                    'subcategoria_slug' => $sub->slug,
                                ]) }}"
                                   class="block text-gray-600 hover:text-pink-500 text-base {{ request()->routeIs('categoria.subcategoria') && request()->route('categoria_slug') == $category->slug && request()->route('subcategoria_slug') == $sub->slug ? 'text-pink-600 font-semibold' : '' }}"
                                   @click="mobileMenu = false">
                                    — {{ $sub->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

        </div>
    </div>
</header>

<style>
  /* Estilo para scrollbar personalizado */
  .custom-scrollbar::-webkit-scrollbar {
    width: 8px;
  }
  
  .custom-scrollbar::-webkit-scrollbar-track {
    background-color: #f9f9f9;
    border-radius: 10px;
  }
  
  .custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #ffb6c1;
    border-radius: 10px;
  }
  
  .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #ff69b4;
  }
  
  /* Limitar líneas de texto */
  .line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
</style>