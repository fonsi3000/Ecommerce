@extends('layouts.app')

@section('title', 'Pedido Confirmado')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-md border border-pink-100 overflow-hidden">
        <!-- Encabezado con mensaje de éxito -->
        <div class="bg-gradient-to-r from-pink-500 to-pink-600 px-6 py-8 text-white text-center">
            <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold mb-2">¡Pedido Confirmado!</h1>
            <p class="text-pink-100">Tu pedido #{{ $order->id }} ha sido recibido y está siendo procesado</p>
        </div>
        
        <!-- Detalles del pedido -->
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Información del pedido -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Detalles del pedido
                    </h2>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Número de pedido</p>
                                <p class="font-medium text-gray-800">#{{ $order->id }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Fecha</p>
                                <p class="font-medium text-gray-800">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Estado</p>
                                <p class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium">
                                    {{ ucfirst($order->status) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500">Total</p>
                                <p class="font-bold text-pink-600">${{ number_format($order->total, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información de contacto -->
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Información de contacto
                    </h2>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-800 font-medium">{{ $order->name }}</p>
                        @if($order->email)
                            <p class="text-gray-600">{{ $order->email }}</p>
                        @endif
                        <p class="text-gray-600">{{ $order->phone }}</p>
                        <p class="text-gray-600">{{ $order->document }}</p>
                        <p class="text-gray-600 mt-2">{{ $order->address }}</p>
                    </div>
                </div>
                
                <!-- Resumen de productos -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Productos
                    </h2>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($order->items as $item)
                                <div class="flex items-center py-3 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-800">{{ $item->name }}</p>
                                        <div class="flex justify-between items-center mt-1 text-sm">
                                            <span class="text-gray-600">{{ $item->quantity }} x ${{ number_format($item->price, 0, ',', '.') }}</span>
                                            <span class="font-semibold text-pink-600">${{ number_format($item->total, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Total del pedido -->
                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <div class="flex justify-between font-bold">
                                <span>Total</span>
                                <span class="text-pink-600">${{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Siguientes pasos -->
                    <div class="bg-pink-50 rounded-lg p-4 border border-pink-100">
                        <h3 class="font-semibold text-gray-800 mb-2">Siguientes pasos:</h3>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-pink-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Te contactaremos por WhatsApp para confirmar los detalles de la entrega.</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-pink-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>El pago se realizará contra entrega en efectivo.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Botones de acción -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('inicio') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-6 rounded-md inline-flex items-center justify-center transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a la tienda
                </a>
                
                <a href="https://wa.me/+573123456789?text=Hola,%20quiero%20consultar%20por%20mi%20pedido%20%23{{ $order->id }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-md inline-flex items-center justify-center transition duration-200">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path>
                    </svg>
                    Consultar por WhatsApp
                </a>
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
</style>
@endsection