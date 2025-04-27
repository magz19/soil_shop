import { useQuery } from "@tanstack/react-query";
import { useParams, useLocation } from "wouter";
import { Product } from "@shared/schema";
import { useCart } from "@/lib/context/CartContext";
import { useState } from "react";
import StarRating from "@/components/ui/StarRating";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Check } from "lucide-react";

const ProductPage = () => {
  const { id } = useParams();
  const [, setLocation] = useLocation();
  const { addToCart } = useCart();
  const [quantity, setQuantity] = useState("1");

  // Fetch product details
  const { data: product, isLoading } = useQuery<Product>({
    queryKey: [`/api/products/${id}`],
  });

  if (isLoading) {
    return (
      <div className="container mx-auto px-4 py-8">
        <div className="flex flex-col md:flex-row gap-8">
          <div className="md:w-2/5">
            <Skeleton className="aspect-square w-full rounded-md" />
          </div>
          <div className="md:w-3/5">
            <Skeleton className="h-8 w-3/4 mb-4" />
            <Skeleton className="h-6 w-1/4 mb-4" />
            <Skeleton className="h-4 w-full mb-3" />
            <Skeleton className="h-4 w-full mb-3" />
            <Skeleton className="h-4 w-full mb-3" />
            <Skeleton className="h-10 w-1/3 mb-4" />
            <Skeleton className="h-4 w-full mb-6" />
            <div className="flex gap-4">
              <Skeleton className="h-12 w-1/2" />
              <Skeleton className="h-12 w-1/2" />
            </div>
          </div>
        </div>
      </div>
    );
  }

  if (!product) {
    return (
      <div className="container mx-auto px-4 py-8">
        <h2 className="text-2xl font-bold text-center">Product not found</h2>
      </div>
    );
  }

  const handleAddToCart = () => {
    addToCart(product, parseInt(quantity));
  };

  const handleBuyNow = () => {
    addToCart(product, parseInt(quantity));
    setLocation("/checkout");
  };

  // Format price with dollar sign
  const formatPrice = (price: number) => {
    const [dollars, cents] = price.toFixed(2).split('.');
    return (
      <>
        <span className="text-sm align-top">$</span>
        <span className="text-3xl font-bold">{dollars}</span>
        <span className="text-lg">.{cents}</span>
      </>
    );
  };

  // Calculate the delivery date
  const getDeliveryDate = (prime: boolean) => {
    const today = new Date();
    const deliveryDate = new Date(today);
    deliveryDate.setDate(today.getDate() + (prime ? 1 : 3));
    
    return deliveryDate.toLocaleDateString('en-US', { 
      weekday: 'long',
      month: 'short', 
      day: 'numeric'
    });
  };

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="flex flex-col md:flex-row gap-8">
        {/* Product Image */}
        <div className="md:w-2/5">
          <div className="bg-white p-4 rounded-md shadow">
            <div className="aspect-square flex items-center justify-center">
              <img 
                src={product.imageUrl} 
                alt={product.name} 
                className="max-h-full max-w-full object-contain"
              />
            </div>
          </div>
        </div>
        
        {/* Product Details */}
        <div className="md:w-3/5">
          <h1 className="text-2xl font-medium mb-2">{product.name}</h1>
          
          <div className="flex items-center mb-2">
            <StarRating rating={product.rating || 0} size="lg" />
            <span className="text-[#007185] ml-2 text-sm">{product.reviewCount} ratings</span>
          </div>
          
          <div className="border-b border-gray-200 pb-4 mb-4">
            <div className="mb-2">
              {product.salePrice ? (
                <>
                  {formatPrice(product.salePrice)}
                  <span className="text-sm text-gray-500 line-through ml-2">
                    ${product.price.toFixed(2)}
                  </span>
                </>
              ) : (
                formatPrice(product.price)
              )}
            </div>
            
            {product.isPrime && (
              <div className="text-sm">
                <span className="text-blue-600 font-bold">
                  <Check className="inline h-4 w-4 mr-1" /> Prime
                </span>
                <span className="ml-2">FREE delivery</span>
              </div>
            )}
          </div>
          
          <div className="mb-6">
            <p className="mb-3">{product.description}</p>
            
            <div className="text-sm mt-4">
              <div className="flex items-start mb-1">
                <span className="font-bold w-24">Availability:</span>
                {product.inStock ? (
                  <span className="text-green-600">In Stock</span>
                ) : (
                  <span className="text-red-600">Out of Stock</span>
                )}
              </div>
              
              <div className="flex items-start mb-1">
                <span className="font-bold w-24">Delivery:</span>
                <span>
                  Get it by <strong>{getDeliveryDate(product.isPrime)}</strong>
                </span>
              </div>
              
              {product.salePrice && (
                <div className="flex items-start mb-1">
                  <span className="font-bold w-24">You Save:</span>
                  <span className="text-green-600">
                    ${(product.price - product.salePrice).toFixed(2)} ({Math.round((1 - product.salePrice / product.price) * 100)}%)
                  </span>
                </div>
              )}
            </div>
          </div>
          
          <div className="bg-gray-50 p-4 rounded-md border border-gray-200 mb-6">
            <div className="flex items-center mb-4">
              <span className="mr-3">Quantity:</span>
              <Select value={quantity} onValueChange={setQuantity}>
                <SelectTrigger className="w-20">
                  <SelectValue placeholder="Qty" />
                </SelectTrigger>
                <SelectContent>
                  {[...Array(10)].map((_, i) => (
                    <SelectItem key={i + 1} value={(i + 1).toString()}>
                      {i + 1}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            
            <div className="flex flex-col sm:flex-row gap-3">
              <Button 
                onClick={handleAddToCart}
                className="bg-[#FFD814] hover:bg-[#F7CA00] text-black font-bold rounded w-full sm:w-1/2"
                disabled={!product.inStock}
              >
                Add to Cart
              </Button>
              <Button 
                onClick={handleBuyNow}
                className="bg-[#FFA41C] hover:bg-[#FF8F00] text-black font-bold rounded w-full sm:w-1/2"
                disabled={!product.inStock}
              >
                Buy Now
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductPage;
