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
            {{-- Buscar con dropdown --}}
            <div class="relative">
                <button @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())" 
                        class="hover:text-pink-600 focus:outline-none"
                        :class="{'text-pink-600': searchOpen}">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                
                <!-- Dropdown del buscador -->
                <div x-show="searchOpen" 
                     @click.outside="searchOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-72 sm:w-80 md:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                    <div class="p-4">
                        <div class="flex items-center border-b border-gray-300 pb-2">
                            <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input 
                                x-ref="searchInput"
                                type="text" 
                                x-model="searchQuery" 
                                @input="performSearch"
                                @keydown.enter="if(searchResults.length > 0) window.location.href='/buscar?q='+searchQuery"
                                placeholder="Buscar productos..." 
                                class="w-full focus:outline-none text-gray-700">
                            <button 
                                x-show="searchQuery.length > 0" 
                                @click="clearSearch" 
                                class="text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Estado de carga -->
                        <div x-show="isSearching" class="py-4 text-center text-gray-500">
                            <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="mt-2">Buscando...</p>
                        </div>
                        
                        <!-- Mensaje de no resultados -->
                        <div x-show="!isSearching && searchQuery.length >= 2 && searchResults.length === 0" class="py-4 text-center text-gray-500">
                            No se encontraron productos
                        </div>
                        
                        <!-- Resultados de búsqueda -->
                        <div x-show="!isSearching && searchResults.length > 0" class="mt-2 max-h-64 overflow-y-auto">
                            <template x-for="producto in searchResults" :key="producto.id">
                                <a :href="'/producto/' + producto.slug" class="block py-2 px-3 hover:bg-pink-50 rounded-md mb-1 transition-colors">
                                    <div class="flex items-center">
                                        <img :src="'/storage/' + producto.image" class="w-12 h-12 object-cover rounded-md" :alt="producto.name">
                                        <div class="ml-3">
                                            <div class="font-semibold text-gray-800" x-text="producto.name"></div>
                                            <div class="text-pink-600" x-text="'$' + Number(producto.price).toLocaleString()"></div>
                                        </div>
                                    </div>
                                </a>
                            </template>
                            
                            <!-- Ver todos los resultados -->
                            <a :href="'/buscar?q=' + searchQuery" class="block text-center py-2 text-pink-600 hover:underline mt-2 border-t border-gray-100 pt-2">
                                Ver todos los resultados
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Carrito (nuevo icono) --}}
            <a href="#" class="relative hover:text-pink-600">
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
            {{-- Campo de búsqueda en menú móvil --}}
            <div class="mb-3 relative">
                <div class="flex items-center border border-gray-300 rounded-full overflow-hidden bg-gray-50 px-3 py-2">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input 
                        type="text" 
                        x-model="searchQuery" 
                        @input="performSearch"
                        placeholder="Buscar productos..." 
                        class="w-full bg-transparent focus:outline-none text-gray-700">
                    <button 
                        x-show="searchQuery.length > 0" 
                        @click="clearSearch" 
                        class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Resultados de búsqueda en móvil -->
                <div 
                    x-show="searchQuery.length >= 2 && searchResults.length > 0" 
                    class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto">
                    <template x-for="producto in searchResults" :key="producto.id">
                        <a :href="'/producto/' + producto.slug" class="block py-2 px-3 hover:bg-pink-50">
                            <div class="flex items-center">
                                <img :src="'/storage/' + producto.image" class="w-10 h-10 object-cover rounded-md" :alt="producto.name">
                                <div class="ml-2">
                                    <div class="font-semibold text-gray-800 text-sm" x-text="producto.name"></div>
                                    <div class="text-pink-600 text-sm" x-text="'$' + Number(producto.price).toLocaleString()"></div>
                                </div>
                            </div>
                        </a>
                    </template>
                    
                    <a :href="'/buscar?q=' + searchQuery" class="block text-center py-2 text-pink-600 hover:underline text-sm border-t border-gray-100">
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