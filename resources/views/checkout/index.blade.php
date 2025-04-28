@extends('layouts.app')

@section('title', 'Finalizar compra')

@section('content')
<style>
  .checkout-page {
    --accent-color: #ff4d94;
    --accent-hover: #e03078;
    --light-accent: #fff1f6;
    --text-color: #4a4a4a;
    --heading-font: 'Playfair Display', serif;
    --body-font: 'Nunito', sans-serif;
    font-family: var(--body-font);
    color: var(--text-color);
  }
  
  .checkout-title {
    font-family: var(--heading-font);
    color: var(--accent-color);
    font-size: 2.2rem;
    margin-bottom: 15px;
    line-height: 1.2;
    position: relative;
    padding-bottom: 10px;
    text-align: center;
  }
  
  .checkout-title:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: linear-gradient(to right, transparent, var(--accent-color), transparent);
  }
  
  .checkout-subtitle {
    font-size: 1.1rem;
    color: #666;
    text-align: center;
    margin-bottom: 2rem;
  }
  
  .checkout-container {
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    position: relative;
    border: 1px solid var(--light-accent);
    margin-bottom: 2rem;
  }
  
  .section-title {
    font-family: var(--heading-font);
    color: var(--accent-color);
    font-size: 1.5rem;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--light-accent);
  }
  
  .form-label {
    font-weight: 600;
    color: #555;
    margin-bottom: 8px;
    font-size: 0.95rem;
    display: block;
  }
  
  .form-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #eee;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-size: 1rem;
    color: var(--text-color);
    background-color: white;
  }
  
  .form-input:focus {
    outline: none;
    border-color: var(--accent-color);
    box-shadow: 0 0 0 3px rgba(255, 77, 148, 0.1);
  }
  
  .form-input.has-error {
    border-color: #e53e3e;
    background-color: #fff5f5;
  }
  
  .error-message {
    color: #e53e3e;
    font-size: 0.85rem;
    margin-top: 5px;
  }
  
  /* Botón de compra */
  .checkout-button {
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
  
  .checkout-button:hover {
    background-color: var(--accent-hover);
    box-shadow: 0 8px 20px rgba(255, 77, 148, 0.4);
    transform: translateY(-2px);
  }
  
  .checkout-button:disabled {
    background-color: #cccccc;
    box-shadow: none;
    cursor: not-allowed;
    transform: none;
  }
  
  /* Resumen del pedido */
  .order-summary {
    position: sticky;
    top: 20px;
  }
  
  .product-card {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid var(--light-accent);
    transition: all 0.3s ease;
  }
  
  .product-card:hover {
    background-color: rgba(255, 77, 148, 0.03);
  }
  
  .product-image {
    width: 70px;
    height: 70px;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #eee;
    background-color: #fafafa;
    margin-right: 15px;
  }
  
  .product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }
  
  .product-info {
    flex: 1;
  }
  
  .product-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .product-quantity {
    color: #666;
    font-size: 0.9rem;
  }
  
  .product-price {
    color: var(--accent-color);
    font-weight: 700;
  }
  
  .summary-total {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-top: 2px solid var(--light-accent);
    margin-top: 15px;
  }
  
  .summary-total-label {
    font-weight: 700;
    font-size: 1.2rem;
    color: #333;
  }
  
  .summary-total-price {
    font-weight: 800;
    font-size: 1.4rem;
    color: var(--accent-color);
  }
  
  .info-text {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    color: #666;
    font-size: 0.9rem;
  }
  
  .info-icon {
    color: #38b2ac;
    margin-right: 10px;
  }
  
  /* Scrollbar personalizado */
  .custom-scrollbar::-webkit-scrollbar {
    width: 8px;
  }
  
  .custom-scrollbar::-webkit-scrollbar-track {
    background-color: rgba(255, 77, 148, 0.05);
    border-radius: 10px;
  }
  
  .custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: var(--accent-color);
    border-radius: 10px;
  }
  
  .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: var(--accent-hover);
  }
  
  /* Alerta de error */
  .error-alert {
    background-color: #fff5f5;
    border-left: 4px solid #e53e3e;
    color: #e53e3e;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
  }
  
  /* Ocultar elementos con Alpine.js */
  [x-cloak] {
    display: none !important;
  }
</style>

<div class="checkout-page">
  <div class="max-w-6xl mx-auto px-4 py-10" x-data="{
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
    <div class="mb-10">
      <h1 class="checkout-title">Finalizar compra</h1>
      <p class="checkout-subtitle">Completa tus datos para procesar el pedido</p>
    </div>
    
    <!-- Alerta de error general -->
    <div x-show="errors.general" class="error-alert" x-cloak>
      <p x-text="errors.general"></p>
    </div>
    
    <div class="grid md:grid-cols-5 gap-8">
      <!-- Formulario de datos (3/5 del ancho) -->
      <div class="md:col-span-3">
        <div class="checkout-container p-6">
          <h2 class="section-title">Información personal</h2>
          
          <div class="space-y-5">
            <!-- Nombre -->
            <div>
              <label for="name" class="form-label">Nombre completo <span class="text-red-500">*</span></label>
              <input type="text" id="name" x-model="formData.name" 
                     class="form-input"
                     :class="{'has-error': errors.name}">
              <p x-show="errors.name" x-text="errors.name" class="error-message" x-cloak></p>
            </div>
            
            <!-- Email (opcional) -->
            <div>
              <label for="email" class="form-label">Correo electrónico (opcional)</label>
              <input type="email" id="email" x-model="formData.email" 
                     class="form-input">
            </div>
            
            <!-- Teléfono -->
            <div>
              <label for="phone" class="form-label">Teléfono <span class="text-red-500">*</span></label>
              <input type="tel" id="phone" x-model="formData.phone" 
                     class="form-input"
                     :class="{'has-error': errors.phone}">
              <p x-show="errors.phone" x-text="errors.phone" class="error-message" x-cloak></p>
            </div>
            
            <!-- Documento (Cédula) -->
            <div>
              <label for="document" class="form-label">Documento/Cédula <span class="text-red-500">*</span></label>
              <input type="text" id="document" x-model="formData.document" 
                     class="form-input"
                     :class="{'has-error': errors.document}">
              <p x-show="errors.document" x-text="errors.document" class="error-message" x-cloak></p>
            </div>
            
            <!-- Dirección -->
            <div>
              <label for="address" class="form-label">Dirección de entrega <span class="text-red-500">*</span></label>
              <textarea id="address" x-model="formData.address" rows="3"
                        class="form-input"
                        :class="{'has-error': errors.address}"></textarea>
              <p x-show="errors.address" x-text="errors.address" class="error-message" x-cloak></p>
            </div>
            
            <div class="mt-8">
              <button @click="submitOrder" 
                      :disabled="loading"
                      class="checkout-button">
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
      </div>
      
      <!-- Resumen del pedido (2/5 del ancho) -->
      <div class="md:col-span-2">
        <div class="checkout-container p-6 order-summary">
          <h2 class="section-title">Resumen del pedido</h2>
          
          <div class="space-y-1 mb-6 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
            <template x-for="(item, index) in cartItems" :key="index">
              <div class="product-card">
                <div class="product-image">
                  <img :src="item.image.startsWith('/storage') ? item.image : '/storage/' + item.image" 
                       :alt="item.name">
                </div>
                <div class="product-info">
                  <p class="product-name" x-text="item.name"></p>
                  <div class="flex justify-between items-center mt-1">
                    <span class="product-quantity">
                      <span x-text="item.quantity"></span> x $<span x-text="item.price.toLocaleString()"></span>
                    </span>
                    <span class="product-price">
                      $<span x-text="(item.price * item.quantity).toLocaleString()"></span>
                    </span>
                  </div>
                </div>
              </div>
            </template>
          </div>
          
          <div class="pt-4 space-y-3">
            <div class="flex justify-between text-gray-600">
              <span class="font-medium">Subtotal</span>
              <span class="font-medium">$<span x-text="cartTotal.toLocaleString()"></span></span>
            </div>
            
            <div class="summary-total">
              <span class="summary-total-label">Total</span>
              <span class="summary-total-price">$<span x-text="cartTotal.toLocaleString()"></span></span>
            </div>
            
            <div class="mt-6 space-y-2">
              <div class="info-text">
                <svg class="w-5 h-5 info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Al finalizar, te contactaremos por WhatsApp para coordinar la entrega</span>
              </div>
              <div class="info-text">
                <svg class="w-5 h-5 info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Pago contra entrega en efectivo</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection