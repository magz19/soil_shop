import { Product, CartItemWithProduct } from "@shared/schema";

// Mock product data
export const products: Product[] = [
  {
    id: 1,
    name: "Smart Watch with Health Tracking and Notifications",
    description: "Track your health metrics and stay connected with this premium smart watch. Features include heart rate monitoring, sleep tracking, and smartphone notifications.",
    price: 249.99,
    salePrice: 199.99,
    imageUrl: "https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=400&h=300",
    category: "Electronics",
    inStock: true,
    rating: 4.5,
    reviewCount: 1245,
    isPrime: true
  },
  {
    id: 2,
    name: "Premium Wireless Headphones with Noise Cancellation",
    description: "Immerse yourself in your music with these premium wireless headphones featuring active noise cancellation, 30-hour battery life, and crystal-clear sound quality.",
    price: 199.99,
    salePrice: 149.99,
    imageUrl: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=400&h=300",
    category: "Electronics",
    inStock: true,
    rating: 5.0,
    reviewCount: 3782,
    isPrime: true
  },
  {
    id: 3,
    name: "Instant Film Camera with Flash and Auto Focus",
    description: "Capture memories instantly with this modern instant film camera featuring built-in flash, auto focus, and high-quality photo prints.",
    price: 119.99,
    salePrice: 89.99,
    imageUrl: "https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?auto=format&fit=crop&w=400&h=300",
    category: "Electronics",
    inStock: true,
    rating: 4.0,
    reviewCount: 856,
    isPrime: true
  },
  {
    id: 4,
    name: "Smart Home Speaker with Voice Assistant",
    description: "Transform your home with this smart speaker featuring a built-in voice assistant, premium sound quality, and smart home control capabilities.",
    price: 99.99,
    salePrice: 79.99,
    imageUrl: "https://images.unsplash.com/photo-1585386959984-a4a9d49e1f90?auto=format&fit=crop&w=400&h=300",
    category: "Electronics",
    inStock: true,
    rating: 3.5,
    reviewCount: 2109,
    isPrime: false
  },
  {
    id: 5,
    name: "Portable Charger 20000mAh Power Bank",
    description: "Keep your devices charged on the go with this high-capacity power bank featuring fast charging and multiple ports.",
    price: 39.99,
    salePrice: 29.99,
    imageUrl: "https://images.unsplash.com/photo-1560769629-975ec94e6a86?auto=format&fit=crop&w=200&h=150",
    category: "Electronics",
    inStock: true,
    rating: 4.5,
    reviewCount: 542,
    isPrime: true
  },
  {
    id: 6,
    name: "Athletic Running Shoes for Men",
    description: "Boost your performance with these lightweight, breathable running shoes designed for comfort and durability.",
    price: 79.99,
    salePrice: 59.99,
    imageUrl: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=200&h=150",
    category: "Fashion",
    inStock: true,
    rating: 4.0,
    reviewCount: 1876,
    isPrime: true
  },
  {
    id: 7,
    name: "Wireless Bluetooth Earbuds with Charging Case",
    description: "Experience true wireless freedom with these Bluetooth earbuds featuring premium sound, long battery life, and comfortable fit.",
    price: 49.99,
    salePrice: 39.99,
    imageUrl: "https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&w=200&h=150",
    category: "Electronics",
    inStock: true,
    rating: 3.5,
    reviewCount: 3211,
    isPrime: true
  },
  {
    id: 8,
    name: "Kitchen Utensil Set - Stainless Steel",
    description: "Complete your kitchen with this comprehensive set of high-quality stainless steel utensils that are durable and dishwasher safe.",
    price: 34.99,
    salePrice: 24.99,
    imageUrl: "https://images.unsplash.com/photo-1581235720704-06d3acfcb36f?auto=format&fit=crop&w=200&h=150",
    category: "Home & Kitchen",
    inStock: true,
    rating: 5.0,
    reviewCount: 762,
    isPrime: true
  }
];
