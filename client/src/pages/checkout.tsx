import { useState } from "react";
import { useLocation } from "wouter";
import { useCart } from "@/lib/context/CartContext";
import { useMutation } from "@tanstack/react-query";
import { apiRequest } from "@/lib/queryClient";
import { useToast } from "@/hooks/use-toast";
import { Separator } from "@/components/ui/separator";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import CheckoutProgress from "@/components/checkout/CheckoutProgress";
import ShippingForm from "@/components/checkout/ShippingForm";
import { InsertOrder, Order } from "@shared/schema";

// Mock user ID for demonstration
const MOCK_USER_ID = 1;

const Checkout = () => {
  const [, setLocation] = useLocation();
  const { cart, clearCart } = useCart();
  const { toast } = useToast();
  const [currentStep, setCurrentStep] = useState<'shipping' | 'payment' | 'review'>('shipping');
  const [shippingMethod, setShippingMethod] = useState('free');
  const [shippingInfo, setShippingInfo] = useState({
    fullName: "",
    phone: "",
    address: "",
    city: "",
    state: "",
    zipCode: "",
  });

  // Calculate subtotal
  const subtotal = cart?.items.reduce((total, item) => {
    const price = item.product.salePrice || item.product.price;
    return total + (price * item.quantity);
  }, 0) || 0;

  // Calculate shipping cost
  const getShippingCost = () => {
    if (shippingMethod === 'free') return 0;
    if (shippingMethod === 'oneDay') return 9.99;
    if (shippingMethod === 'sameDay') return 14.99;
    return 0;
  };

  // Calculate total
  const total = subtotal + getShippingCost();

  // Place order mutation
  const placeOrderMutation = useMutation({
    mutationFn: async (orderData: InsertOrder) => {
      const res = await apiRequest('POST', '/api/orders', orderData);
      return res.json() as Promise<Order>;
    },
    onSuccess: (data) => {
      toast({
        title: "Order Placed Successfully!",
        description: `Your order #${data.id} has been placed.`,
      });
      clearCart();
      setLocation(`/orders/${data.id}`);
    },
    onError: (error) => {
      toast({
        title: "Error placing order",
        description: error.message,
        variant: "destructive",
      });
    }
  });

  const handleShippingSubmit = (values: any) => {
    setShippingInfo(values);
    setCurrentStep('payment');
  };

  const handlePaymentSubmit = () => {
    setCurrentStep('review');
  };

  const handlePlaceOrder = () => {
    if (!cart) return;
    
    const orderData: InsertOrder = {
      userId: MOCK_USER_ID,
      total: total,
      status: 'pending',
      shippingAddress: shippingInfo.address,
      shippingCity: shippingInfo.city,
      shippingState: shippingInfo.state,
      shippingZip: shippingInfo.zipCode,
      shippingMethod: shippingMethod
    };
    
    placeOrderMutation.mutate(orderData);
  };

  if (!cart || cart.items.length === 0) {
    return (
      <div className="container mx-auto px-4 py-8 text-center">
        <h2 className="text-2xl font-bold mb-4">Your cart is empty</h2>
        <p className="mb-6">Add some items to your cart before checking out.</p>
        <Button 
          onClick={() => setLocation("/")}
          className="bg-[#FFD814] hover:bg-[#F7CA00] text-black font-bold"
        >
          Continue Shopping
        </Button>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold mb-6">Checkout</h1>
      
      {/* Progress Indicator */}
      <CheckoutProgress currentStep={currentStep} />
      
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main Content */}
        <div className="lg:col-span-2">
          <div className="bg-white rounded-md shadow p-6">
            {currentStep === 'shipping' && (
              <>
                <h2 className="text-lg font-bold mb-4">Shipping Address</h2>
                <ShippingForm 
                  onSubmit={handleShippingSubmit}
                  defaultValues={shippingInfo}
                />
                
                <Separator className="my-6" />
                
                <h3 className="text-lg font-bold mb-4">Shipping Options</h3>
                <RadioGroup 
                  value={shippingMethod} 
                  onValueChange={setShippingMethod}
                  className="space-y-3"
                >
                  <div className="flex items-start p-3 border border-gray-300 rounded cursor-pointer hover:border-[#007185]">
                    <RadioGroupItem value="free" id="free-shipping" className="mt-1 mr-3" />
                    <div className="flex-grow">
                      <Label htmlFor="free-shipping" className="font-medium">FREE Prime Delivery</Label>
                      <p className="text-sm text-gray-500">Get it by Tuesday, Oct 17</p>
                    </div>
                    <div className="ml-auto font-bold">FREE</div>
                  </div>
                  
                  <div className="flex items-start p-3 border border-gray-300 rounded cursor-pointer hover:border-[#007185]">
                    <RadioGroupItem value="oneDay" id="one-day" className="mt-1 mr-3" />
                    <div className="flex-grow">
                      <Label htmlFor="one-day" className="font-medium">One-Day Delivery</Label>
                      <p className="text-sm text-gray-500">Get it Tomorrow</p>
                    </div>
                    <div className="ml-auto font-bold">$9.99</div>
                  </div>
                  
                  <div className="flex items-start p-3 border border-gray-300 rounded cursor-pointer hover:border-[#007185]">
                    <RadioGroupItem value="sameDay" id="same-day" className="mt-1 mr-3" />
                    <div className="flex-grow">
                      <Label htmlFor="same-day" className="font-medium">Same-Day Delivery</Label>
                      <p className="text-sm text-gray-500">Get it Today by 10PM</p>
                    </div>
                    <div className="ml-auto font-bold">$14.99</div>
                  </div>
                </RadioGroup>
                
                <div className="flex justify-between mt-6">
                  <Button 
                    variant="link" 
                    className="text-[#007185] p-0"
                    onClick={() => setLocation("/cart")}
                  >
                    Return to Cart
                  </Button>
                  <Button 
                    onClick={() => handleShippingSubmit(shippingInfo)}
                    className="bg-[#FFD814] hover:bg-[#F7CA00] text-black font-bold"
                  >
                    Continue to Payment
                  </Button>
                </div>
              </>
            )}
            
            {currentStep === 'payment' && (
              <>
                <h2 className="text-lg font-bold mb-4">Payment Method</h2>
                
                {/* Mock payment form */}
                <div className="space-y-4">
                  <div>
                    <Label>Card Number</Label>
                    <input 
                      type="text" 
                      className="w-full border border-gray-300 rounded px-3 py-2 mt-1" 
                      placeholder="1234 5678 9012 3456" 
                    />
                  </div>
                  
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <Label>Expiration Date</Label>
                      <input 
                        type="text" 
                        className="w-full border border-gray-300 rounded px-3 py-2 mt-1" 
                        placeholder="MM/YY" 
                      />
                    </div>
                    <div>
                      <Label>Security Code</Label>
                      <input 
                        type="text" 
                        className="w-full border border-gray-300 rounded px-3 py-2 mt-1" 
                        placeholder="CVV" 
                      />
                    </div>
                  </div>
                  
                  <div>
                    <Label>Name on Card</Label>
                    <input 
                      type="text" 
                      className="w-full border border-gray-300 rounded px-3 py-2 mt-1" 
                      placeholder="John Doe" 
                    />
                  </div>
                </div>
                
                <div className="flex justify-between mt-6">
                  <Button 
                    variant="link" 
                    className="text-[#007185] p-0"
                    onClick={() => setCurrentStep('shipping')}
                  >
                    Back to Shipping
                  </Button>
                  <Button 
                    onClick={handlePaymentSubmit}
                    className="bg-[#FFD814] hover:bg-[#F7CA00] text-black font-bold"
                  >
                    Review Order
                  </Button>
                </div>
              </>
            )}
            
            {currentStep === 'review' && (
              <>
                <h2 className="text-lg font-bold mb-4">Review Your Order</h2>
                
                <div className="space-y-6">
                  {/* Shipping Info */}
                  <div>
                    <h3 className="font-medium mb-2">Shipping Address</h3>
                    <div className="bg-gray-50 p-3 rounded">
                      <p>{shippingInfo.fullName}</p>
                      <p>{shippingInfo.address}</p>
                      <p>{shippingInfo.city}, {shippingInfo.state} {shippingInfo.zipCode}</p>
                      <p>{shippingInfo.phone}</p>
                    </div>
                  </div>
                  
                  {/* Shipping Method */}
                  <div>
                    <h3 className="font-medium mb-2">Shipping Method</h3>
                    <div className="bg-gray-50 p-3 rounded">
                      {shippingMethod === 'free' && <p>FREE Prime Delivery (2-3 days)</p>}
                      {shippingMethod === 'oneDay' && <p>One-Day Delivery ($9.99)</p>}
                      {shippingMethod === 'sameDay' && <p>Same-Day Delivery ($14.99)</p>}
                    </div>
                  </div>
                  
                  {/* Order Items */}
                  <div>
                    <h3 className="font-medium mb-2">Order Items</h3>
                    <div className="space-y-3">
                      {cart.items.map(item => (
                        <div key={item.id} className="flex items-center">
                          <div className="w-16 h-16 flex-shrink-0">
                            <img 
                              src={item.product.imageUrl} 
                              alt={item.product.name}
                              className="w-full h-full object-contain" 
                            />
                          </div>
                          <div className="ml-3 flex-grow">
                            <p className="font-medium line-clamp-1">{item.product.name}</p>
                            <p className="text-sm text-gray-500">Qty: {item.quantity}</p>
                          </div>
                          <div className="ml-auto">
                            ${((item.product.salePrice || item.product.price) * item.quantity).toFixed(2)}
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
                
                <div className="flex justify-between mt-6">
                  <Button 
                    variant="link" 
                    className="text-[#007185] p-0"
                    onClick={() => setCurrentStep('payment')}
                  >
                    Back to Payment
                  </Button>
                  <Button 
                    onClick={handlePlaceOrder}
                    className="bg-[#FFD814] hover:bg-[#F7CA00] text-black font-bold"
                    disabled={placeOrderMutation.isPending}
                  >
                    {placeOrderMutation.isPending ? "Processing..." : "Place Order"}
                  </Button>
                </div>
              </>
            )}
          </div>
        </div>
        
        {/* Order Summary */}
        <div className="lg:col-span-1">
          <div className="bg-white rounded-md shadow p-4 sticky top-4">
            <h2 className="text-lg font-bold mb-3">Order Summary</h2>
            
            <div className="space-y-2 text-sm mb-4">
              <div className="flex justify-between">
                <span>Items ({cart.items.reduce((acc, item) => acc + item.quantity, 0)}):</span>
                <span>${subtotal.toFixed(2)}</span>
              </div>
              <div className="flex justify-between">
                <span>Shipping:</span>
                <span>
                  {getShippingCost() === 0 ? 'FREE' : `$${getShippingCost().toFixed(2)}`}
                </span>
              </div>
              <div className="flex justify-between">
                <span>Estimated tax:</span>
                <span>$0.00</span>
              </div>
            </div>
            
            <Separator />
            
            <div className="flex justify-between font-bold text-lg mt-4">
              <span>Order total:</span>
              <span>${total.toFixed(2)}</span>
            </div>
            
            {currentStep === 'review' && (
              <Button 
                onClick={handlePlaceOrder}
                className="bg-[#FFD814] hover:bg-[#F7CA00] text-black font-bold w-full mt-4"
                disabled={placeOrderMutation.isPending}
              >
                {placeOrderMutation.isPending ? "Processing..." : "Place Order"}
              </Button>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default Checkout;
