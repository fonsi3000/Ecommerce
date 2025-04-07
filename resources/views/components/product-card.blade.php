<style>
    .product-card {
      --card-radius: 12px;
      --accent-color: #ff4d94;
      --dark-color: #1a1a2e;
      --light-color: #f8f8f8;
      
      background: linear-gradient(135deg, var(--light-color), white);
      border-radius: var(--card-radius);
      overflow: hidden;
      position: relative;
      transform: translateY(0);
      transition: all 0.3s ease;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
      display: block;
      text-decoration: none;
      color: var(--dark-color);
    }
    
    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 30px rgba(0, 0, 0, 0.1);
    }
    
    .product-card:after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 5px;
      background: linear-gradient(90deg, var(--accent-color), #a239ca);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.4s ease;
    }
    
    .product-card:hover:after {
      transform: scaleX(1);
    }
    
    .product-image-container {
      position: relative;
      height: 260px; /* Aumentado de 220px a 260px */
      overflow: hidden;
    }
    
    .product-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.8s ease;
    }
    
    .product-card:hover .product-image {
      transform: scale(1.05);
    }
    
    .product-details {
      padding: 1.8rem; /* Aumentado de 1.5rem a 1.8rem */
      position: relative;
    }
    
    .product-name {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 0.8rem; /* Aumentado de 0.5rem a 0.8rem */
      color: var(--dark-color);
      line-height: 1.4;
    }
    
    .product-price {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--accent-color);
      display: flex;
      align-items: center;
      margin-top: 0.5rem; /* Añadido margen superior */
    }
    
    /* Eliminada la rayita lateral del precio */
    
    .add-to-cart {
      position: absolute;
      right: 1.8rem;
      bottom: 1.8rem;
      background-color: var(--accent-color);
      color: white;
      width: 42px; /* Ligeramente más grande */
      height: 42px; /* Ligeramente más grande */
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transform: translateY(10px);
      opacity: 0;
      transition: all 0.3s ease;
    }
    
    .product-card:hover .add-to-cart {
      transform: translateY(0);
      opacity: 1;
    }
    
    .add-to-cart svg {
      width: 20px;
      height: 20px;
    }
    
    @media (max-width: 768px) {
      .product-image-container {
        height: 220px; /* Ajustado para móviles pero aún más alto que el original */
      }
      .product-details {
        padding: 1.2rem;
      }
      .product-name {
        font-size: 1rem;
      }
      .product-price {
        font-size: 1.1rem;
      }
    }
  </style>
  
  <a href="{{ route('producto.show', $producto->slug) }}" class="product-card">
    <div class="product-image-container">
      <img src="{{ asset('storage/' . $producto->image) }}" alt="{{ $producto->name }}" class="product-image">
    </div>
    <div class="product-details">
      <h2 class="product-name">{{ $producto->name }}</h2>
      <p class="product-price">${{ number_format($producto->price, 0, ',', '.') }}</p>
      <div class="add-to-cart">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"></path>
          <path d="M16 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"></path>
          <path d="M3 3h2l2 14h11"></path>
          <path d="M11 7h8l-2 8H9"></path>
        </svg>
      </div>
    </div>
  </a>