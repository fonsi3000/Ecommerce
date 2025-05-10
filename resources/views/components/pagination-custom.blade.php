@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex items-center justify-center mt-6 space-x-1 text-sm">
        
        {{-- Botón Anterior --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 rounded-md border border-gray-300 text-gray-400 bg-white">
                ⬅️ Atrás
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 rounded-md border border-pink-500 text-pink-600 hover:bg-pink-100">
                ⬅️ Atrás
            </a>
        @endif

        {{-- Números de página --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-1 text-gray-500">...</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 rounded-md bg-pink-600 text-white font-bold">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-pink-100">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Botón Siguiente --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 rounded-md border border-pink-500 text-pink-600 hover:bg-pink-100">
                Siguiente ➡️
            </a>
        @else
            <span class="px-3 py-1 rounded-md border border-gray-300 text-gray-400 bg-white">
                Siguiente ➡️
            </span>
        @endif
    </nav>
@endif
