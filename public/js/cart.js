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
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ ids: productIds })
                })
                .then(res => res.json())
                .then(products => {
                    this.stockInfo = {};
                    products.forEach(product => {
                        if (product.variants && product.variants.length > 0) {
                            const variantStocks = {};
                            product.variants.forEach(variant => {
                                variantStocks[variant.id] = variant.stock;
                            });
                            this.stockInfo[product.id] = {
                                name: product.name,
                                stock: product.stock,
                                variantStocks: variantStocks
                            };
                        } else {
                            this.stockInfo[product.id] = {
                                name: product.name,
                                stock: product.stock
                            };
                        }
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

                    let availableStock = item.variant_id && productStock.variantStocks
                        ? productStock.variantStocks[item.variant_id] || 0
                        : productStock.stock || 0;

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
                const stock = variantId && this.stockInfo[id].variantStocks
                    ? this.stockInfo[id].variantStocks[variantId] || 0
                    : this.stockInfo[id].stock || 0;
                return qty <= stock;
            },

            getAvailableStock(id, variantId) {
                if (!this.stockInfo[id]) return 999;
                if (variantId && this.stockInfo[id].variantStocks) {
                    return this.stockInfo[id].variantStocks[variantId] || 0;
                }
                return this.stockInfo[id].stock || 0;
            },

            addItem(product) {
                const key = product.variant_id ? `${product.id}-${product.variant_id}` : `${product.id}`;
                const qty = parseInt(product.quantity) || 1;

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
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        },
                        body: JSON.stringify({ items: itemsToSend })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.valid === true) {
                            window.location.href = '/checkout';
                            resolve(true);
                        } else {
                            if (data.errors && data.errors.length > 0) {
                                data.errors.forEach(err => {
                                    const item = this.items.find(i =>
                                        i.id === err.id && (!err.variant_id || i.variant_id === err.variant_id)
                                    );

                                    if (item) {
                                        item.quantity = Math.min(item.quantity, err.available_stock || 0);
                                        this.showStockNotification(item.name, err.available_stock || 0, err.requested_quantity);
                                    }
                                });
                                this.saveCart();
                                reject('Hay productos con stock insuficiente en tu carrito');
                            } else {
                                reject('La validación de stock falló. Intenta nuevamente.');
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error en la validación:', err);
                        alert(err.message || 'Error al validar disponibilidad de productos');
                        reject(err.message || 'Error al validar disponibilidad de productos');
                    });
                });
            }
        });
    } else {
        console.warn('⚠️ El store `cart` ya está registrado');
    }
});
