import { 
  users, type User, type InsertUser,
  products, type Product, type InsertProduct,
  carts, type Cart, type InsertCart,
  cartItems, type CartItem, type InsertCartItem, type CartItemWithProduct,
  orders, type Order, type InsertOrder,
  orderItems, type OrderItem, type InsertOrderItem, type OrderItemWithProduct
} from "@shared/schema";

// modify the interface with any CRUD methods
// you might need

export interface IStorage {
  // User methods
  getUser(id: number): Promise<User | undefined>;
  getUserByUsername(username: string): Promise<User | undefined>;
  createUser(user: InsertUser): Promise<User>;
  
  // Product methods
  getAllProducts(): Promise<Product[]>;
  getProduct(id: number): Promise<Product | undefined>;
  getProductsByCategory(category: string): Promise<Product[]>;
  
  // Cart methods
  getCart(userId: number): Promise<Cart | undefined>;
  getCartWithProducts(userId: number): Promise<{ id: number; userId: number; items: CartItemWithProduct[] } | undefined>;
  addToCart(userId: number, productId: number, quantity: number): Promise<CartItemWithProduct>;
  updateCartItemQuantity(cartItemId: number, quantity: number): Promise<CartItem>;
  removeCartItem(cartItemId: number): Promise<void>;
  
  // Order methods
  createOrder(orderData: InsertOrder): Promise<Order>;
  getOrderWithItems(orderId: number): Promise<{ id: number; userId: number; items: OrderItemWithProduct[] } | undefined>;
  getUserOrders(userId: number): Promise<Order[]>;
  getAllOrders(): Promise<Order[]>;
  updateOrderStatus(orderId: number, status: string): Promise<Order | undefined>;
}

export class MemStorage implements IStorage {
  private users: Map<number, User>;
  private products: Map<number, Product>;
  private carts: Map<number, Cart>;
  private cartItems: Map<number, CartItem>;
  private orders: Map<number, Order>;
  private orderItems: Map<number, OrderItem>;
  
  currentId: number;
  private cartId: number;
  private cartItemId: number;
  private orderId: number;
  private orderItemId: number;

  constructor() {
    this.users = new Map();
    this.products = new Map();
    this.carts = new Map();
    this.cartItems = new Map();
    this.orders = new Map();
    this.orderItems = new Map();
    
    this.currentId = 1;
    this.cartId = 1;
    this.cartItemId = 1;
    this.orderId = 1;
    this.orderItemId = 1;
    
    // Initialize with S-Oil products
    this.initSoilProducts();
  }

  private initSoilProducts() {
    const soilProducts: Product[] = [
      {
        id: 1,
        name: "S-Oil Ultra Synthetic 5W-30 Motor Oil",
        description: "Premium full synthetic engine oil that provides exceptional wear protection, enhanced fuel economy and outstanding engine cleanliness.",
        price: 24.99,
        salePrice: 21.99,
        imageUrl: "https://images.unsplash.com/photo-1635273051936-7069a5e709a2?auto=format&fit=crop&w=400&h=300",
        category: "Motor Oil",
        inStock: true,
        rating: 4.8,
        reviewCount: 234,
        isPrime: true
      },
      {
        id: 2,
        name: "S-Oil Seven Dragon 10W-40 Semi-Synthetic Oil",
        description: "Semi-synthetic oil that offers excellent protection against engine wear and tear. Ideal for high-mileage vehicles.",
        price: 19.99,
        salePrice: 17.99,
        imageUrl: "https://images.unsplash.com/photo-1613177794106-be20802b11d3?auto=format&fit=crop&w=400&h=300",
        category: "Motor Oil",
        inStock: true,
        rating: 4.5,
        reviewCount: 189,
        isPrime: true
      },
      {
        id: 3,
        name: "S-Oil Transmission Fluid ATF",
        description: "High-quality automatic transmission fluid ensuring smooth gear shifting and maximum protection for your transmission system.",
        price: 15.99,
        salePrice: 14.50,
        imageUrl: "https://images.unsplash.com/photo-1694487410292-94577041fddc?auto=format&fit=crop&w=400&h=300",
        category: "Transmission Fluid",
        inStock: true,
        rating: 4.6,
        reviewCount: 112,
        isPrime: true
      },
      {
        id: 4,
        name: "S-Oil Brake Fluid DOT 4",
        description: "High performance brake fluid with excellent resistance to moisture absorption. Provides reliable braking performance even under extreme conditions.",
        price: 12.99,
        salePrice: 10.99,
        imageUrl: "https://images.unsplash.com/photo-1651678463794-7d991c0cf9a8?auto=format&fit=crop&w=400&h=300",
        category: "Brake Fluid",
        inStock: true,
        rating: 4.7,
        reviewCount: 97,
        isPrime: true
      },
      {
        id: 5,
        name: "S-Oil Antifreeze Coolant",
        description: "All-season engine coolant that protects your cooling system from freezing and overheating. Contains anti-corrosion additives.",
        price: 14.99,
        salePrice: 12.99,
        imageUrl: "https://images.unsplash.com/photo-1600436518453-3c33d55326a5?auto=format&fit=crop&w=400&h=300",
        category: "Coolant",
        inStock: true,
        rating: 4.4,
        reviewCount: 78,
        isPrime: true
      },
      {
        id: 6,
        name: "S-Oil Power Steering Fluid",
        description: "Specially formulated to protect power steering systems. Prevents leaks and ensures smooth operation of the steering mechanism.",
        price: 9.99,
        salePrice: null,
        imageUrl: "https://images.unsplash.com/photo-1607200319809-6b420ae7ca22?auto=format&fit=crop&w=400&h=300",
        category: "Steering Fluid",
        inStock: true,
        rating: 4.3,
        reviewCount: 56,
        isPrime: false
      },
      {
        id: 7,
        name: "S-Oil Hydraulic Oil ISO 46",
        description: "Premium hydraulic oil that provides excellent wear protection and oxidation stability for hydraulic systems operating under high pressure.",
        price: 29.99,
        salePrice: 25.99,
        imageUrl: "https://images.unsplash.com/photo-1613361581093-2bbf4b19275e?auto=format&fit=crop&w=400&h=300",
        category: "Hydraulic Oil",
        inStock: true,
        rating: 4.9,
        reviewCount: 45,
        isPrime: true
      },
      {
        id: 8,
        name: "S-Oil Industrial Grease",
        description: "Multi-purpose lubricating grease for industrial and automotive applications. Provides excellent protection against wear, water, and heat.",
        price: 8.99,
        salePrice: 7.50,
        imageUrl: "https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=400&h=300",
        category: "Grease",
        inStock: true,
        rating: 4.6,
        reviewCount: 38,
        isPrime: false
      }
    ];
    
    soilProducts.forEach(product => {
      this.products.set(product.id, product);
    });
  }

  // User Methods
  async getUser(id: number): Promise<User | undefined> {
    return this.users.get(id);
  }

  async getUserByUsername(username: string): Promise<User | undefined> {
    return Array.from(this.users.values()).find(
      (user) => user.username === username,
    );
  }

  async createUser(insertUser: InsertUser): Promise<User> {
    const id = this.currentId++;
    const user: User = { 
      ...insertUser, 
      id, 
      address: insertUser.address || null,
      city: insertUser.city || null,
      state: insertUser.state || null,
      zipCode: insertUser.zipCode || null,
      phone: insertUser.phone || null 
    };
    this.users.set(id, user);
    return user;
  }
  
  // Product Methods
  async getAllProducts(): Promise<Product[]> {
    return Array.from(this.products.values());
  }
  
  async getProduct(id: number): Promise<Product | undefined> {
    return this.products.get(id);
  }
  
  async getProductsByCategory(category: string): Promise<Product[]> {
    return Array.from(this.products.values()).filter(
      (product) => product.category === category
    );
  }
  
  // Cart Methods
  async getCart(userId: number): Promise<Cart | undefined> {
    return Array.from(this.carts.values()).find(
      (cart) => cart.userId === userId
    );
  }
  
  async getCartWithProducts(userId: number): Promise<{ id: number; userId: number; items: CartItemWithProduct[] } | undefined> {
    // Find or create cart for user
    let cart = await this.getCart(userId);
    
    if (!cart) {
      cart = {
        id: this.cartId++,
        userId,
        createdAt: new Date()
      };
      this.carts.set(cart.id, cart);
    }
    
    // Get cart items with product details
    const items = Array.from(this.cartItems.values())
      .filter((item) => item.cartId === cart.id)
      .map((item) => {
        const product = this.products.get(item.productId);
        return {
          ...item,
          product: product!
        };
      });
    
    return {
      id: cart.id,
      userId: cart.userId,
      items
    };
  }
  
  async addToCart(userId: number, productId: number, quantity: number): Promise<CartItemWithProduct> {
    // Find or create cart
    let cart = await this.getCart(userId);
    
    if (!cart) {
      cart = {
        id: this.cartId++,
        userId,
        createdAt: new Date()
      };
      this.carts.set(cart.id, cart);
    }
    
    // Check if product exists
    const product = await this.getProduct(productId);
    if (!product) {
      throw new Error("Product not found");
    }
    
    // Check if item already in cart
    const existingItem = Array.from(this.cartItems.values()).find(
      (item) => item.cartId === cart!.id && item.productId === productId
    );
    
    if (existingItem) {
      // Update quantity
      existingItem.quantity += quantity;
      this.cartItems.set(existingItem.id, existingItem);
      
      return {
        ...existingItem,
        product
      };
    } else {
      // Add new item
      const cartItem: CartItem = {
        id: this.cartItemId++,
        cartId: cart.id,
        productId,
        quantity
      };
      
      this.cartItems.set(cartItem.id, cartItem);
      
      return {
        ...cartItem,
        product
      };
    }
  }
  
  async updateCartItemQuantity(cartItemId: number, quantity: number): Promise<CartItem> {
    const cartItem = this.cartItems.get(cartItemId);
    
    if (!cartItem) {
      throw new Error("Cart item not found");
    }
    
    cartItem.quantity = quantity;
    this.cartItems.set(cartItemId, cartItem);
    
    return cartItem;
  }
  
  async removeCartItem(cartItemId: number): Promise<void> {
    this.cartItems.delete(cartItemId);
  }
  
  // Order Methods
  async createOrder(orderData: InsertOrder): Promise<Order> {
    const order: Order = {
      ...orderData,
      id: this.orderId++,
      createdAt: new Date()
    };
    
    this.orders.set(order.id, order);
    
    // Get cart and create order items
    const cart = await this.getCartWithProducts(orderData.userId);
    
    if (cart && cart.items.length > 0) {
      cart.items.forEach((item) => {
        const orderItem: OrderItem = {
          id: this.orderItemId++,
          orderId: order.id,
          productId: item.productId,
          price: item.product.salePrice || item.product.price,
          quantity: item.quantity
        };
        
        this.orderItems.set(orderItem.id, orderItem);
      });
      
      // Clear cart after order is created
      cart.items.forEach((item) => {
        this.cartItems.delete(item.id);
      });
    }
    
    return order;
  }
  
  async getOrderWithItems(orderId: number): Promise<{ id: number; userId: number; items: OrderItemWithProduct[] } | undefined> {
    const order = this.orders.get(orderId);
    
    if (!order) {
      return undefined;
    }
    
    // Get order items with product details
    const items = Array.from(this.orderItems.values())
      .filter((item) => item.orderId === orderId)
      .map((item) => {
        const product = this.products.get(item.productId);
        return {
          ...item,
          product: product!
        };
      });
    
    return {
      ...order,
      items
    };
  }
  
  async getUserOrders(userId: number): Promise<Order[]> {
    return Array.from(this.orders.values())
      .filter((order) => order.userId === userId)
      .sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime()); // Sort newest first
  }
  
  async getAllOrders(): Promise<Order[]> {
    return Array.from(this.orders.values())
      .sort((a, b) => b.createdAt.getTime() - a.createdAt.getTime()); // Sort newest first
  }
  
  async updateOrderStatus(orderId: number, status: string): Promise<Order | undefined> {
    const order = this.orders.get(orderId);
    
    if (!order) {
      return undefined;
    }
    
    order.status = status;
    this.orders.set(orderId, order);
    
    return order;
  }
}

export const storage = new MemStorage();
