{{-- Alpine.js desde CDN (sin Vite) --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<header x-data="{ mobileMenu: false }" class="bg-white shadow-sm sticky top-0 z-50">
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
            {{-- Buscar --}}
            <a href="#" class="hover:text-pink-600">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </a>

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