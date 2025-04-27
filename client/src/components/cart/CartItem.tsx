import { useState } from "react";
import { Link } from "wouter";
import { useCart } from "@/lib/context/CartContext";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Button } from "@/components/ui/button";
import { CartItemWithProduct } from "@shared/schema";

interface CartItemProps {
  item: CartItemWithProduct;
}

const CartItem = ({ item }: CartItemProps) => {
  const { updateCartItemQuantity, removeCartItem } = useCart();
  const [quantity, setQuantity] = useState(item.quantity.toString());
  
  const handleQuantityChange = (value: string) => {
    setQuantity(value);
    updateCartItemQuantity(item.id, parseInt(value));
  };
  
  const handleDelete = () => {
    removeCartItem(item.id);
  };
  
  const handleSaveForLater = () => {
    // In a real app, this would move the item to a "Saved for Later" list
    console.log("Save for later", item);
  };
  
  return (
    <div className="py-4 flex flex-col sm:flex-row border-b">
      <div className="w-full sm:w-24 flex-shrink-0 mb-4 sm:mb-0">
        <Link href={`/product/${item.product.id}`}>
          <a>
            <img 
              src={item.product.imageUrl} 
              alt={item.product.name} 
              className="w-24 h-24 object-contain mx-auto"
            />
          </a>
        </Link>
      </div>
      
      <div className="flex-grow px-4">
        <Link href={`/product/${item.product.id}`}>
          <a>
            <h3 className="font-medium text-[#007185] hover:text-[#C7511F] hover:underline">
              {item.product.name}
            </h3>
          </a>
        </Link>
        <p className="text-green-600 text-sm">
          {item.product.inStock ? "In Stock" : "Out of Stock"}
        </p>
        <p className="text-sm text-gray-500">Eligible for FREE Shipping</p>
        
        <div className="flex flex-wrap items-center mt-2">
          <div className="flex mr-4 mb-2">
            <Select value={quantity} onValueChange={handleQuantityChange}>
              <SelectTrigger className="w-20 h-8">
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
          
          <Button 
            variant="link" 
            className="text-[#007185] hover:text-[#C7511F] hover:underline text-sm mr-4 mb-2 p-0 h-auto"
            onClick={handleDelete}
          >
            Delete
          </Button>
          
          <Button 
            variant="link" 
            className="text-[#007185] hover:text-[#C7511F] hover:underline text-sm mb-2 p-0 h-auto"
            onClick={handleSaveForLater}
          >
            Save for later
          </Button>
        </div>
      </div>
      
      <div className="text-right">
        <p className="font-bold">
          ${(item.product.salePrice || item.product.price).toFixed(2)}
        </p>
      </div>
    </div>
  );
};

export default CartItem;
