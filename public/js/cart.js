document.addEventListener('alpine:init', () => {
    // Verifica si ya existe para no duplicar
    if (!Alpine.store('cart')) {
        Alpine.store('cart', {
            items: [],
            isOpen: false,

            init() {
                this.loadCart();
                console.log('✅ Alpine store `cart` inicializado');
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

            addItem(product) {
                const key = product.variant_id ? `${product.id}-${product.variant_id}` : `${product.id}`;
                const qty = parseInt(product.quantity) || 1;

                const existing = this.items.find(i => {
                    const itemKey = i.variant_id ? `${i.id}-${i.variant_id}` : `${i.id}`;
                    return itemKey === key;
                });

                if (existing) {
                    existing.quantity += qty;
                    console.log('🔁 Producto actualizado:', existing);
                } else {
                    const newItem = {
                        id: product.id,
                        name: product.name,
                        price: parseFloat(product.price),
                        image: product.image,
                        quantity: qty,
                        variant_id: product.variant_id || null
                    };
                    this.items.push(newItem);
                    console.log('➕ Nuevo producto:', newItem);
                }

                this.saveCart();
                this.isOpen = true;
            },

            removeItem(id, variant_id) {
                const key = variant_id ? `${id}-${variant_id}` : `${id}`;
                this.items = this.items.filter(i =>
                    (i.variant_id ? `${i.id}-${i.variant_id}` : `${i.id}`) !== key
                );
                this.saveCart();
            },

            updateQuantity(id, variant_id, qty) {
                const key = variant_id ? `${id}-${variant_id}` : `${id}`;
                const item = this.items.find(i =>
                    (i.variant_id ? `${i.id}-${i.variant_id}` : `${i.id}`) === key
                );
            
                if (item) {
                    item.quantity = qty;
                    if (item.quantity <= 0) {
                        this.removeItem(id, variant_id);
                    } else {
                        this.saveCart();
                    }
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
            }
        });
    } else {
        console.warn('⚠️ El store `cart` ya está registrado');
    }
});
