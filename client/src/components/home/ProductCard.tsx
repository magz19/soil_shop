import { Link } from "wouter";
import { useCart } from "@/lib/context/CartContext";
import { Product } from "@shared/schema";
import StarRating from "@/components/ui/StarRating";
import { Button } from "@/components/ui/button";
import { Check } from "lucide-react";

interface ProductCardProps {
  product: Product;
}

const ProductCard = ({ product }: ProductCardProps) => {
  const { addToCart } = useCart();

  const handleAddToCart = () => {
    addToCart(product, 1);
  };

  const handleBuyNow = () => {
    addToCart(product, 1);
    window.location.href = "/checkout";
  };

  // Format price with dollar sign
  const formatPrice = (price: number) => {
    const [dollars, cents] = price.toFixed(2).split('.');
    return (
      <>
        <span className="text-xs align-top">$</span>
        <span className="text-lg font-bold">{dollars}</span>
        <span className="text-xs">.{cents}</span>
      </>
    );
  };

  // Calculate the delivery date
  const getDeliveryDate = () => {
    const today = new Date();
    const deliveryDate = new Date(today);
    deliveryDate.setDate(today.getDate() + (product.isPrime ? 1 : 3));
    
    return deliveryDate.toLocaleDateString('en-US', { 
      weekday: 'long',
      month: 'short', 
      day: 'numeric'
    });
  };

  return (
    <div className="bg-white rounded-md shadow p-4 transition-transform duration-200 product-card">
      <div className="relative">
        {product.salePrice && (
          <span className="absolute top-0 left-0 bg-[#FF9900] text-black text-xs font-bold px-2 py-1 rounded-br-md">
            SALE
          </span>
        )}
        <Link href={`/product/${product.id}`}>
          <a className="block">
            <div className="h-48 flex items-center justify-center mb-3">
              <img 
                src={product.imageUrl} 
                alt={product.name} 
                className="max-h-full max-w-full object-contain"
              />
            </div>
          </a>
        </Link>
      </div>
      
      <Link href={`/product/${product.id}`}>
        <a className="block">
          <h3 className="font-medium text-sm line-clamp-2 h-10">{product.name}</h3>
        </a>
      </Link>
      
      <div className="flex items-center mt-1">
        <StarRating rating={product.rating || 0} />
        <span className="text-xs text-gray-500 ml-1">{product.reviewCount}</span>
      </div>
      
      <div className="mt-2">
        {product.salePrice ? (
          <>
            {formatPrice(product.salePrice)}
            <span className="text-xs text-gray-500 line-through ml-1">
              ${product.price.toFixed(2)}
            </span>
          </>
        ) : (
          formatPrice(product.price)
        )}
      </div>
      
      <div className="text-xs text-green-600 font-medium mt-1">
        {product.isPrime && (
          <>
            <Check className="inline h-3 w-3" /> Prime
          </>
        )}
        <span className="text-black ml-2">
          Get it by <strong>{getDeliveryDate()}</strong>
        </span>
      </div>
      
      <div className="flex space-x-2 mt-3">
        <Button 
          onClick={handleAddToCart}
          className="bg-[#FFD814] hover:bg-[#F7CA00] text-black text-xs font-bold py-1 px-2 rounded flex-grow"
        >
          Add to Cart
        </Button>
        <Button 
          onClick={handleBuyNow}
          className="bg-[#FFA41C] hover:bg-[#FF8F00] text-black text-xs font-bold py-1 px-2 rounded flex-grow"
        >
          Buy Now
        </Button>
      </div>
    </div>
  );
};

export default ProductCard;
