@extends('layouts.app')

@section('title', 'Finalizar compra')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8" x-data="{
    loading: false,
    formData: {
        name: '',
        email: '',
        phone: '',
        document: '',
        address: ''
    },
    errors: {},
    cartItems: [],
    cartTotal: 0,
    
    // Inicializa datos
    init() {
        // Esperar hasta que Alpine y el store estén disponibles
        const waitForStore = () => {
            if (Alpine && Alpine.store && Alpine.store('cart')) {
                this.cartItems = Alpine.store('cart').items;
                this.cartTotal = Alpine.store('cart').getTotalPrice();
            } else {
                setTimeout(waitForStore, 50);
            }
        };
        waitForStore();
        
        // Si no hay productos, redireccionar al inicio
        if (this.cartItems.length === 0) {
            window.location.href = '{{ route('inicio') }}';
        }
    },
    
    // Envía el formulario
    submitOrder() {
        this.loading = true;
        this.errors = {};
        
        // Validación básica
        if (!this.formData.name) this.errors.name = 'El nombre es obligatorio';
        if (!this.formData.phone) this.errors.phone = 'El teléfono es obligatorio';
        if (!this.formData.document) this.errors.document = 'El documento es obligatorio';
        if (!this.formData.address) this.errors.address = 'La dirección es obligatoria';
        
        // Si hay errores, detener
        if (Object.keys(this.errors).length > 0) {
            this.loading = false;
            return;
        }
        
        // Preparar los datos del formulario
        const data = new FormData();
        data.append('name', this.formData.name);
        data.append('email', this.formData.email);
        data.append('phone', this.formData.phone);
        data.append('document', this.formData.document);
        data.append('address', this.formData.address);
        data.append('cart_items', JSON.stringify(this.cartItems));
        
        // Enviar petición al servidor
        fetch('{{ route('checkout.store') }}', {
            method: 'POST',
            body: data,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            this.loading = false;
            
            if (data.success) {
                // Limpiar el carrito
                Alpine.store('cart').clearCart();
                
                // Redireccionar a WhatsApp
                window.location.href = data.whatsapp_url;
            } else {
                // Mostrar errores
                if (data.errors) {
                    this.errors = data.errors;
                } else {
                    this.errors.general = data.message || 'Ocurrió un error al procesar la orden';
                }
            }
        })
        .catch(error => {
            this.loading = false;
            this.errors.general = 'Error de conexión. Intenta nuevamente.';
            console.error('Error:', error);
        });
    }
}">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-pink-600 mb-2">Finalizar compra</h1>
        <p class="text-gray-600">Completa tus datos para procesar el pedido</p>
    </div>
    
    <!-- Alerta de error general -->
    <div x-show="errors.general" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" x-cloak>
        <p x-text="errors.general"></p>
    </div>
    
    <div class="grid md:grid-cols-5 gap-8">
        <!-- Formulario de datos (3/5 del ancho) -->
        <div class="md:col-span-3 bg-white rounded-lg shadow-md p-6 border border-pink-100">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Información personal</h2>
            
            <div class="space-y-4">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                    <input type="text" id="name" x-model="formData.name" 
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition-colors"
                           :class="errors.name ? 'border-red-300 bg-red-50' : 'border-gray-300'">
                    <p x-show="errors.name" x-text="errors.name" class="mt-1 text-sm text-red-500" x-cloak></p>
                </div>
                
                <!-- Email (opcional) -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico (opcional)</label>
                    <input type="email" id="email" x-model="formData.email" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition-colors">
                </div>
                
                <!-- Teléfono -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-red-500">*</span></label>
                    <input type="tel" id="phone" x-model="formData.phone" 
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition-colors"
                           :class="errors.phone ? 'border-red-300 bg-red-50' : 'border-gray-300'">
                    <p x-show="errors.phone" x-text="errors.phone" class="mt-1 text-sm text-red-500" x-cloak></p>
                </div>
                
                <!-- Documento (Cédula) -->
                <div>
                    <label for="document" class="block text-sm font-medium text-gray-700 mb-1">Documento/Cédula <span class="text-red-500">*</span></label>
                    <input type="text" id="document" x-model="formData.document" 
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition-colors"
                           :class="errors.document ? 'border-red-300 bg-red-50' : 'border-gray-300'">
                    <p x-show="errors.document" x-text="errors.document" class="mt-1 text-sm text-red-500" x-cloak></p>
                </div>
                
                <!-- Dirección -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Dirección de entrega <span class="text-red-500">*</span></label>
                    <textarea id="address" x-model="formData.address" rows="3"
                              class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition-colors resize-none"
                              :class="errors.address ? 'border-red-300 bg-red-50' : 'border-gray-300'"></textarea>
                    <p x-show="errors.address" x-text="errors.address" class="mt-1 text-sm text-red-500" x-cloak></p>
                </div>
                
                <div class="mt-6">
                    <button @click="submitOrder" 
                            :disabled="loading"
                            class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-4 rounded-md shadow-md transition duration-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50 flex items-center justify-center">
                        <span x-show="loading" class="inline-block mr-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        <span x-text="loading ? 'Procesando...' : 'Pagar ahora'"></span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Resumen del pedido (2/5 del ancho) -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 border border-pink-100 sticky top-24">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Resumen del pedido</h2>
                
                <div class="space-y-4 mb-6 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                    <template x-for="(item, index) in cartItems" :key="index">
                        <div class="flex items-center space-x-4 py-3 border-b border-gray-100">
                            <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-md overflow-hidden">
                                <img :src="item.image.startsWith('/storage') ? item.image : '/storage/' + item.image" 
                                     class="w-full h-full object-cover" 
                                     :alt="item.name">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 truncate" x-text="item.name"></p>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-gray-600 text-sm">
                                        <span x-text="item.quantity"></span> x $<span x-text="item.price.toLocaleString()"></span>
                                    </span>
                                    <span class="font-semibold text-pink-600">
                                        $<span x-text="(item.price * item.quantity).toLocaleString()"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="border-t border-gray-200 pt-4 space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium">$<span x-text="cartTotal.toLocaleString()"></span></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-800">
                        <span>Total</span>
                        <span class="text-pink-600">$<span x-text="cartTotal.toLocaleString()"></span></span>
                    </div>
                    
                    <div class="mt-6 text-sm text-gray-500">
                        <p class="flex items-center mb-1">
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Al finalizar, te contactaremos por WhatsApp para coordinar la entrega
                        </p>
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Pago contra entrega en efectivo
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    
    /* Ocultar elementos con Alpine.js */
    [x-cloak] {
        display: none !important;
    }
</style>
@endsection