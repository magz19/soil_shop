import { Link, useLocation } from "wouter";
import { useCart } from "@/lib/context/CartContext";
import CartItem from "@/components/cart/CartItem";
import { Button } from "@/components/ui/button";
import { Separator } from "@/components/ui/separator";
import { AlertCircle, ShoppingCart } from "lucide-react";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";

const Cart = () => {
  const { cart } = useCart();
  const [, setLocation] = useLocation();

  // Calculate subtotal
  const subtotal = cart?.items.reduce((total, item) => {
    const price = item.product.salePrice || item.product.price;
    return total + (price * item.quantity);
  }, 0) || 0;

  // Check if cart is empty
  const isCartEmpty = !cart || cart.items.length === 0;

  const handleCheckout = () => {
    setLocation("/checkout");
  };

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold mb-6">Shopping Cart</h1>
      
      {isCartEmpty ? (
        <div className="bg-white rounded-md shadow p-6 text-center">
          <div className="flex justify-center mb-4">
            <ShoppingCart className="h-16 w-16 text-gray-400" />
          </div>
          <h2 className="text-xl font-bold mb-2">Your Amazon Cart is empty</h2>
          <p className="text-gray-600 mb-4">
            Your shopping cart is waiting. Give it purpose – fill it with groceries, clothing, household supplies, electronics, and more.
          </p>
          <Link href="/">
            <Button className="bg-[#FFD814] hover:bg-[#F7CA00] text-black font-bold">
              Continue Shopping
            </Button>
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
          {/* Cart Items */}
          <div className="lg:col-span-3">
            <div className="bg-white rounded-md shadow p-6">
              <div className="flex justify-between mb-4">
                <h2 className="text-xl font-bold">Cart ({cart.items.length} items)</h2>
                <span className="text-gray-500">Price</span>
              </div>
              
              <div className="divide-y">
                {cart.items.map((item) => (
                  <CartItem key={item.id} item={item} />
                ))}
              </div>
              
              <div className="flex justify-end mt-4 text-right">
                <p className="text-lg">
                  Subtotal ({cart.items.reduce((acc, item) => acc + item.quantity, 0)} items): 
                  <span className="font-bold ml-2">${subtotal.toFixed(2)}</span>
                </p>
              </div>
            </div>
            
            {/* Recently viewed items would go here */}
          </div>
          
          {/* Checkout Card */}
          <div className="lg:col-span-1">
            <div className="bg-white rounded-md shadow p-4">
              <div className="mb-4">
                {subtotal >= 25 ? (
                  <Alert className="bg-green-50 border-green-200">
                    <AlertCircle className="h-4 w-4 text-green-600" />
                    <AlertTitle className="text-green-600 font-bold">
                      Your order qualifies for FREE Shipping
                    </AlertTitle>
                    <AlertDescription className="text-green-600 text-sm">
                      Choose this option at checkout.
                    </AlertDescription>
                  </Alert>
                ) : (
                  <Alert className="bg-blue-50 border-blue-200">
                    <AlertCircle className="h-4 w-4 text-blue-600" />
                    <AlertTitle className="text-blue-600 font-bold">
                      Add ${(25 - subtotal).toFixed(2)} more for FREE Shipping
                    </AlertTitle>
                  </Alert>
                )}
              </div>
              
              <div className="text-right mb-4">
                <p className="text-lg">
                  Subtotal ({cart.items.reduce((acc, item) => acc + item.quantity, 0)} items): 
                  <span className="font-bold ml-2">${subtotal.toFixed(2)}</span>
                </p>
              </div>
              
              <Button 
                onClick={handleCheckout}
                className="bg-[#FFD814] hover:bg-[#F7CA00] text-black font-bold w-full"
              >
                Proceed to Checkout
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default Cart;
