@extends('layouts.app')

@section('title', 'Mi Carrito')

@section('content')
<div x-data="{ cart: $store.cart }" class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

    <h1 class="text-3xl font-bold mb-6 text-pink-600">Tu Carrito</h1>

    <!-- Carrito vacío -->
    <template x-if="cart.items.length === 0">
        <div class="text-center text-gray-500 py-20">
            <p class="text-lg">Tu carrito está vacío 😕</p>
            <a href="{{ route('inicio') }}" class="text-pink-500 hover:underline mt-4 inline-block">Volver al inicio</a>
        </div>
    </template>

    <!-- Carrito con productos -->
    <template x-if="cart.items.length > 0">
        <div class="bg-white shadow-md rounded-lg p-6">
            <table class="w-full table-auto text-left">
                <thead>
                    <tr class="text-gray-700 border-b">
                        <th class="py-3">Producto</th>
                        <th class="py-3 text-center">Cantidad</th>
                        <th class="py-3 text-right">Precio</th>
                        <th class="py-3 text-right">Total</th>
                        <th class="py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in cart.items" :key="item.id">
                        <tr class="border-b hover:bg-pink-50/30">
                            <td class="py-4 flex items-center gap-4">
                                <img :src="item.image" class="h-14 w-14 rounded border border-gray-200 object-cover">
                                <div>
                                    <div class="font-semibold text-gray-800" x-text="item.name"></div>
                                    <div class="text-xs text-gray-500">ID: <span x-text="item.id"></span></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="number" min="1" class="w-16 border rounded px-2 py-1 text-center" 
                                       x-model.number="item.quantity" @change="cart.save()">
                            </td>
                            <td class="text-right text-pink-600 font-medium" x-text="'$' + item.price.toLocaleString()"></td>
                            <td class="text-right font-semibold text-gray-700" x-text="'$' + (item.quantity * item.price).toLocaleString()"></td>
                            <td class="text-right">
                                <button @click="cart.remove(item.id)" class="text-red-500 hover:text-red-700 text-sm">Eliminar</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <div class="flex justify-between items-center mt-6 border-t pt-4">
                <div>
                    <button @click="cart.clear()" class="text-sm text-red-500 hover:underline">Vaciar carrito</button>
                </div>
                <div class="text-lg font-semibold text-gray-800">
                    Total: <span x-text="'$' + cart.totalPrice().toLocaleString()"></span>
                </div>
            </div>

            <div class="mt-6 text-right">
                <form method="POST" action="{{ route('cart.validate.stock') }}" 
                      @submit.prevent="validarCarrito($store.cart.items)">
                    @csrf
                    <button type="submit"
                        class="bg-pink-500 text-white px-6 py-2 rounded hover:bg-pink-600 transition-all font-medium">
                        Finalizar compra
                    </button>
                </form>
            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
function validarCarrito(items) {
    fetch('{{ route('cart.validate.stock') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ items }),
    })
    .then(res => {
        if (!res.ok) throw res;
        return res.json();
    })
    .then(data => {
        alert('Carrito validado correctamente. Procede al pago.');
        // Aquí puedes redirigir a /checkout o mostrar modal
    })
    .catch(async (err) => {
        let errorText = 'Ocurrió un error de validación.';
        try {
            const json = await err.json();
            if (json.errors) {
                errorText = json.errors.map(e => e.error).join('\n');
            }
        } catch {}
        alert(errorText);
    });
}
</script>
@endpush
@endsection
