document.addEventListener('alpine:init', () => {
    if (!Alpine.store('cart')) {
        Alpine.store('cart', {
            items: [],
            isOpen: false,
            stockInfo: {},
            loadingStock: false,

            init() {
                this.loadCart();
                console.log('✅ Alpine store `cart` inicializado');
                this.refreshStockInfo();
            },

            loadCart() {
                try {
                    const savedCart = localStorage.getItem('cart');
                    if (savedCart) {
                        this.items = JSON.parse(savedCart);
                        console.log('🛒 Carrito cargado:', this.items);
                    }
                } catch (error) {
                    console.error('❌ Error al cargar el carrito:', error);
                    this.items = [];
                }
            },

            saveCart() {
                try {
                    localStorage.setItem('cart', JSON.stringify(this.items));
                } catch (error) {
                    console.error('❌ Error al guardar el carrito:', error);
                }
            },

            getCsrfToken() {
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                if (metaToken) return metaToken.getAttribute('content');

                const csrfInput = document.querySelector('input[name="_token"]');
                if (csrfInput) return csrfInput.value;

                const cookies = document.cookie.split(';');
                for (let cookie of cookies) {
                    cookie = cookie.trim();
                    if (cookie.startsWith('XSRF-TOKEN=')) {
                        return decodeURIComponent(cookie.substring('XSRF-TOKEN='.length));
                    }
                }

                console.error('No se pudo encontrar el token CSRF.');
                return '';
            },

            refreshStockInfo() {
                if (this.items.length === 0) return;

                this.loadingStock = true;
                const productIds = [...new Set(this.items.map(item => item.id))];

                fetch('/carrito/info-productos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ ids: productIds })
                })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`Error del servidor: ${res.status}`);
                    }
                    return res.json();
                })
                .then(products => {
                    this.stockInfo = {};
                    products.forEach(product => {
                        const stockData = {
                            name: product.name,
                            stock: product.stock
                        };

                        // Si el producto tiene variantes
                        if (product.variants && Object.keys(product.variants).length > 0) {
                            stockData.variantStocks = {};
                            
                            // Procesar cada variante
                            Object.keys(product.variants).forEach(variantId => {
                                // Si es un objeto con info adicional
                                if (typeof product.variants[variantId] === 'object') {
                                    stockData.variantStocks[variantId] = product.variants[variantId].stock;
                                    
                                    // Si es una variante "sin atributo", guardamos su info
                                    if (product.variants[variantId].atributo_tipo === 'ninguno') {
                                        stockData.noAttributeVariantId = variantId;
                                        stockData.noAttributeStock = product.variants[variantId].stock;
                                    }
                                } else {
                                    // Si es directamente el valor del stock
                                    stockData.variantStocks[variantId] = product.variants[variantId];
                                }
                            });
                        }
                        
                        this.stockInfo[product.id] = stockData;
                    });
                    this.validateAllQuantities();
                    this.loadingStock = false;
                })
                .catch(error => {
                    console.error('Error al obtener información de productos:', error);
                    this.loadingStock = false;
                });
            },

            validateAllQuantities() {
                let cartChanged = false;

                this.items.forEach(item => {
                    const productStock = this.stockInfo[item.id];
                    if (!productStock) return;

                    let availableStock;
                    
                    // Si el item tiene variante específica y existe información de esa variante
                    if (item.variant_id && productStock.variantStocks && productStock.variantStocks[item.variant_id]) {
                        availableStock = productStock.variantStocks[item.variant_id];
                    } 
                    // Si el item no tiene variante pero hay una variante "sin atributo"
                    else if (!item.variant_id && productStock.noAttributeVariantId) {
                        availableStock = productStock.noAttributeStock;
                        // Asignar la variante "sin atributo" al item
                        item.variant_id = productStock.noAttributeVariantId;
                        cartChanged = true;
                    }
                    // Caso de producto sin variantes
                    else {
                        availableStock = productStock.stock || 0;
                    }

                    if (item.quantity > availableStock) {
                        const oldQty = item.quantity;
                        item.quantity = Math.max(1, availableStock);
                        cartChanged = true;
                        this.showStockNotification(item.name, availableStock, oldQty);
                    }
                });

                if (cartChanged) this.saveCart();
            },

            showStockNotification(name, available, requested) {
                const notif = document.createElement('div');
                notif.className = 'fixed bottom-4 right-4 bg-pink-50 border-l-4 border-pink-500 text-pink-700 p-4 rounded shadow-lg z-50 max-w-md';
                notif.style.animation = 'slideInRight 0.3s ease-out forwards';
                notif.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium">Stock limitado</h3>
                            <div class="mt-1 text-sm text-pink-700">
                                <p>Solo hay ${available} unidades disponibles de "${name}".</p>
                                <p class="mt-1">Se ha ajustado la cantidad en tu carrito.</p>
                            </div>
                            <div class="mt-2 flex">
                                <button type="button" class="close-notification text-sm font-medium text-pink-700 hover:text-pink-500 focus:outline-none">
                                    Entendido
                                </button>
                            </div>
                        </div>
                    </div>`;

                document.body.appendChild(notif);

                if (!document.getElementById('notification-styles')) {
                    const style = document.createElement('style');
                    style.id = 'notification-styles';
                    style.textContent = `
                        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
                        @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
                    `;
                    document.head.appendChild(style);
                }

                notif.querySelector('.close-notification').addEventListener('click', () => {
                    notif.style.animation = 'slideOutRight 0.3s ease-in forwards';
                    setTimeout(() => notif.remove(), 300);
                });

                setTimeout(() => {
                    if (document.body.contains(notif)) {
                        notif.style.animation = 'slideOutRight 0.3s ease-in forwards';
                        setTimeout(() => notif.remove(), 300);
                    }
                }, 4000);
            },

            hasEnoughStock(id, variantId, qty) {
                if (!this.stockInfo[id]) return true;
                
                // Si hay variante especificada y el producto tiene información de variantes
                if (variantId && this.stockInfo[id].variantStocks) {
                    return qty <= (this.stockInfo[id].variantStocks[variantId] || 0);
                } 
                // Si hay una variante "sin atributo" y no se especificó una variante
                else if (!variantId && this.stockInfo[id].noAttributeVariantId) {
                    return qty <= (this.stockInfo[id].noAttributeStock || 0);
                }
                // Caso de producto sin variante - verificar stock principal
                else {
                    return qty <= (this.stockInfo[id].stock || 0);
                }
            },

            getAvailableStock(id, variantId) {
                if (!this.stockInfo[id]) return 999;
                
                // Si hay variante especificada y el producto tiene información de variantes
                if (variantId && this.stockInfo[id].variantStocks) {
                    return parseInt(this.stockInfo[id].variantStocks[variantId] || 0);
                }
                // Si hay una variante "sin atributo" y no se especificó una variante
                else if (!variantId && this.stockInfo[id].noAttributeVariantId) {
                    return parseInt(this.stockInfo[id].noAttributeStock || 0);
                }
                // Caso de producto sin variante - usar stock principal
                else {
                    return parseInt(this.stockInfo[id].stock || 0);
                }
            },

            addItem(product) {
                const key = product.variant_id ? `${product.id}-${product.variant_id}` : `${product.id}`;
                const qty = parseInt(product.quantity) || 1;

                // Si el producto no tiene variante pero hay variante "sin atributo" en el sistema
                if (!product.variant_id && this.stockInfo[product.id] && this.stockInfo[product.id].noAttributeVariantId) {
                    product.variant_id = this.stockInfo[product.id].noAttributeVariantId;
                }

                let availableStock = this.getAvailableStock(product.id, product.variant_id);

                const existing = this.items.find(i => {
                    const itemKey = i.variant_id ? `${i.id}-${i.variant_id}` : `${i.id}`;
                    return itemKey === key;
                });

                if (existing) {
                    const newQty = existing.quantity + qty;
                    existing.quantity = newQty > availableStock ? availableStock : newQty;
                    if (newQty > availableStock) this.showStockNotification(existing.name, availableStock, newQty);
                } else {
                    const newItem = {
                        id: product.id,
                        name: product.name,
                        price: parseFloat(product.price),
                        image: product.image,
                        quantity: Math.min(qty, availableStock),
                        variant_id: product.variant_id || null
                    };
                    this.items.push(newItem);
                    if (qty > availableStock) this.showStockNotification(product.name, availableStock, qty);
                }

                this.saveCart();
                this.isOpen = true;
                setTimeout(() => this.refreshStockInfo(), 100);
            },

            removeItem(id, variantId) {
                const key = variantId ? `${id}-${variantId}` : `${id}`;
                this.items = this.items.filter(i => {
                    const itemKey = i.variant_id ? `${i.id}-${i.variant_id}` : `${i.id}`;
                    return itemKey !== key;
                });
                this.saveCart();
            },

            updateQuantity(id, variantId, qty) {
                const key = variantId ? `${id}-${variantId}` : `${id}`;
                const item = this.items.find(i => {
                    const itemKey = i.variant_id ? `${i.id}-${i.variant_id}` : `${i.id}`;
                    return itemKey === key;
                });

                if (item) {
                    if (qty <= 0) {
                        this.removeItem(id, variantId);
                        return;
                    }

                    const availableStock = this.getAvailableStock(id, variantId);
                    if (qty > availableStock) {
                        item.quantity = availableStock;
                        this.showStockNotification(item.name, availableStock, qty);
                    } else {
                        item.quantity = qty;
                    }

                    this.saveCart();
                    this.refreshStockInfo();
                }
            },

            clearCart() {
                this.items = [];
                this.saveCart();
            },

            getTotalItems() {
                return this.items.reduce((sum, i) => sum + i.quantity, 0);
            },

            getTotalPrice() {
                return this.items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            },

            validateCheckout() {
                return new Promise((resolve, reject) => {
                    if (this.items.length === 0) {
                        window.location.href = '/checkout';
                        return resolve(true);
                    }

                    const itemsToSend = this.items.map(i => ({
                        id: i.id,
                        variant_id: i.variant_id || null,
                        quantity: i.quantity
                    }));

                    fetch('/carrito/validar-stock', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.getCsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ items: itemsToSend })
                    })
                    .then(res => {
                        // Verificar si la respuesta es OK antes de intentar convertirla a JSON
                        if (!res.ok) {
                            return res.text().then(text => {
                                // Intenta parsear el texto como JSON
                                try {
                                    return JSON.parse(text);
                                } catch (e) {
                                    // Si no es JSON, lanza un error con el texto
                                    console.error("Respuesta no válida:", text);
                                    throw new Error(`Error del servidor: ${res.status}. Respuesta no es JSON válido.`);
                                }
                            });
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.valid === true) {
                            window.location.href = '/checkout';
                            resolve(true);
                        } else {
                            if (data.errors && data.errors.length > 0) {
                                data.errors.forEach(err => {
                                    // Mejorado para encontrar correctamente tanto productos con variantes como sin ellas
                                    const item = this.items.find(i => {
                                        if (i.id === err.id) {
                                            // Para productos con variante
                                            if (err.variant_id) {
                                                return i.variant_id === err.variant_id;
                                            }
                                            // Para productos sin variante
                                            else {
                                                return i.variant_id === null || i.variant_id === undefined;
                                            }
                                        }
                                        return false;
                                    });

                                    if (item) {
                                        item.quantity = Math.min(item.quantity, err.available_stock || 0);
                                        this.showStockNotification(item.name, err.available_stock || 0, err.requested_quantity);
                                    }
                                });
                                this.saveCart();
                                reject('Hay productos con stock insuficiente en tu carrito');
                            } else {
                                reject(data.message || 'La validación de stock falló. Intenta nuevamente.');
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error en la validación:', err);
                        alert(err.message || 'Error al validar disponibilidad de productos. Por favor, recarga la página e intenta de nuevo.');
                        reject(err.message || 'Error al validar disponibilidad de productos');
                    });
                });
            }
        });
    } else {
        console.warn('⚠️ El store `cart` ya está registrado');
    }
});