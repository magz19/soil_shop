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
import { Input } from "@/components/ui/input";
import CheckoutProgress from "@/components/checkout/CheckoutProgress";
import { InsertOrder, Order } from "@shared/schema";
import { CreditCard, MapPin, Phone, User, Mail } from "lucide-react";

// Mock user ID for demonstration
const MOCK_USER_ID = 1;

const Checkout = () => {
  const [, setLocation] = useLocation();
  const { cart, clearCart } = useCart();
  const { toast } = useToast();
  const [currentStep, setCurrentStep] = useState<'shipping' | 'payment' | 'review'>('shipping');
  const [shippingMethod, setShippingMethod] = useState('pickup');
  const [billingInfo, setBillingInfo] = useState({
    fullName: "",
    phone: "",
    email: "",
    address: "",
    city: "",
    state: "",
    zipCode: "",
  });

  // Calculate subtotal
  const subtotal = cart?.items.reduce((total, item) => {
    const price = item.product.sale_price || item.product.price;
    return total + (price * item.quantity);
  }, 0) || 0;

  // Calculate total (no shipping cost)
  const total = subtotal;

  // Handle form input change
  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setBillingInfo(prev => ({ ...prev, [name]: value }));
  };

  // Check if form is complete
  const isFormComplete = () => {
    const { fullName, phone, email, address, city, state, zipCode } = billingInfo;
    return fullName && phone && email && address && city && state && zipCode;
  };

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

  const handleShippingSubmit = () => {
    if (!isFormComplete()) {
      toast({
        title: "Please fill all fields",
        description: "All billing details are required to place an order.",
        variant: "destructive",
      });
      return;
    }
    
    setCurrentStep('review');
  };

  const handlePlaceOrder = () => {
    if (!cart) return;
    
    const orderData: InsertOrder = {
      userId: MOCK_USER_ID,
      total: total,
      status: 'pending',
      shippingAddress: billingInfo.address,
      shippingCity: billingInfo.city,
      shippingState: billingInfo.state,
      shippingZip: billingInfo.zipCode,
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
      <CheckoutProgress currentStep={currentStep === 'shipping' ? 'shipping' : 'review'} />
      
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main Content */}
        <div className="lg:col-span-2">
          <div className="bg-white rounded-md shadow p-6">
            {currentStep === 'shipping' && (
              <>
                <h2 className="text-lg font-bold mb-4">Billing Details</h2>
                <div className="space-y-4">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <Label htmlFor="fullName">Full Name <span className="text-red-500">*</span></Label>
                      <div className="flex items-center mt-1">
                        <User className="h-4 w-4 text-gray-400 absolute ml-3" />
                        <Input 
                          id="fullName"
                          name="fullName"
                          value={billingInfo.fullName}
                          onChange={handleInputChange}
                          placeholder="John Doe"
                          className="pl-10"
                          required
                        />
                      </div>
                    </div>
                    
                    <div>
                      <Label htmlFor="phone">Phone Number <span className="text-red-500">*</span></Label>
                      <div className="flex items-center mt-1">
                        <Phone className="h-4 w-4 text-gray-400 absolute ml-3" />
                        <Input 
                          id="phone"
                          name="phone"
                          value={billingInfo.phone}
                          onChange={handleInputChange}
                          placeholder="(123) 456-7890"
                          className="pl-10"
                          required
                        />
                      </div>
                    </div>
                  </div>
                  
                  <div>
                    <Label htmlFor="email">Email <span className="text-red-500">*</span></Label>
                    <div className="flex items-center mt-1">
                      <Mail className="h-4 w-4 text-gray-400 absolute ml-3" />
                      <Input 
                        id="email"
                        name="email"
                        type="email"
                        value={billingInfo.email}
                        onChange={handleInputChange}
                        placeholder="your@email.com"
                        className="pl-10"
                        required
                      />
                    </div>
                  </div>
                  
                  <div>
                    <Label htmlFor="address">Address <span className="text-red-500">*</span></Label>
                    <div className="flex items-center mt-1">
                      <MapPin className="h-4 w-4 text-gray-400 absolute ml-3" />
                      <Input 
                        id="address"
                        name="address"
                        value={billingInfo.address}
                        onChange={handleInputChange}
                        placeholder="123 Main St"
                        className="pl-10"
                        required
                      />
                    </div>
                  </div>
                  
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <Label htmlFor="city">City <span className="text-red-500">*</span></Label>
                      <Input 
                        id="city"
                        name="city"
                        value={billingInfo.city}
                        onChange={handleInputChange}
                        placeholder="Manila"
                        required
                      />
                    </div>
                    
                    <div>
                      <Label htmlFor="state">State/Province <span className="text-red-500">*</span></Label>
                      <Input 
                        id="state"
                        name="state"
                        value={billingInfo.state}
                        onChange={handleInputChange}
                        placeholder="Metro Manila"
                        required
                      />
                    </div>
                    
                    <div>
                      <Label htmlFor="zipCode">ZIP Code <span className="text-red-500">*</span></Label>
                      <Input 
                        id="zipCode"
                        name="zipCode"
                        value={billingInfo.zipCode}
                        onChange={handleInputChange}
                        placeholder="1008"
                        required
                      />
                    </div>
                  </div>
                </div>
                
                <Separator className="my-6" />
                
                <h3 className="text-lg font-bold mb-4">Shipping Options</h3>
                <RadioGroup 
                  value={shippingMethod} 
                  onValueChange={setShippingMethod}
                  className="space-y-3"
                >
                  <div className="flex items-start p-3 border border-gray-300 rounded cursor-pointer hover:border-[#007185]">
                    <RadioGroupItem value="pickup" id="pickup" className="mt-1 mr-3" />
                    <div className="flex-grow">
                      <Label htmlFor="pickup" className="font-medium">Personal Pickup</Label>
                      <p className="text-sm text-gray-500">Address: 123 Mendiola St. Manila City</p>
                      <p className="text-sm text-gray-500">Contact: Anjhela Geron 09454545</p>
                    </div>
                    <div className="ml-auto font-bold">FREE</div>
                  </div>
                  
                  <div className="flex items-start p-3 border border-gray-300 rounded cursor-pointer hover:border-[#007185]">
                    <RadioGroupItem value="delivery" id="delivery" className="mt-1 mr-3" />
                    <div className="flex-grow">
                      <Label htmlFor="delivery" className="font-medium">Grab/Lalamove Delivery</Label>
                      <p className="text-sm text-gray-500">Arranged by client (shipping fee not included)</p>
                    </div>
                    <div className="ml-auto font-bold">Arranged by client</div>
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
                    onClick={handleShippingSubmit}
                    className="bg-[#FFD814] hover:bg-[#F7CA00] text-black font-bold"
                  >
                    Continue to Review
                  </Button>
                </div>
              </>
            )}
            
            {currentStep === 'review' && (
              <>
                <h2 className="text-lg font-bold mb-4">Review Your Order</h2>
                
                <div className="space-y-6">
                  {/* Billing Info */}
                  <div>
                    <h3 className="font-medium mb-2">Billing Details</h3>
                    <div className="bg-gray-50 p-3 rounded">
                      <p>{billingInfo.fullName}</p>
                      <p>{billingInfo.email}</p>
                      <p>{billingInfo.phone}</p>
                      <p>{billingInfo.address}</p>
                      <p>{billingInfo.city}, {billingInfo.state} {billingInfo.zipCode}</p>
                    </div>
                  </div>
                  
                  {/* Shipping Method */}
                  <div>
                    <h3 className="font-medium mb-2">Shipping Method</h3>
                    <div className="bg-gray-50 p-3 rounded">
                      {shippingMethod === 'pickup' && (
                        <>
                          <p className="font-medium">Personal Pickup</p>
                          <p>Address: 123 Mendiola St. Manila City</p>
                          <p>Contact: Anjhela Geron 09454545</p>
                        </>
                      )}
                      {shippingMethod === 'delivery' && (
                        <>
                          <p className="font-medium">Grab/Lalamove Delivery</p>
                          <p>Arranged by client (shipping fee not included)</p>
                        </>
                      )}
                    </div>
                  </div>
                  
                  {/* Payment Method */}
                  <div>
                    <h3 className="font-medium mb-2">Payment Information</h3>
                    <div className="bg-gray-50 p-3 rounded">
                      <div className="flex items-center space-x-2">
                        <CreditCard className="h-4 w-4 text-gray-500" />
                        <span className="font-medium">GCash Payment</span>
                      </div>
                      <p className="mt-1">Martin Magno 091234567</p>
                      
                      <div className="mt-3 text-sm border-t pt-2 text-gray-500">
                        <p>Please make the payment to the account above and send a screenshot of the transaction to the store.</p>
                        <p>You can also select "Over-the-counter" payment upon receiving your order.</p>
                      </div>
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
                              src={item.product.image_url} 
                              alt={item.product.name}
                              className="w-full h-full object-contain" 
                            />
                          </div>
                          <div className="ml-3 flex-grow">
                            <p className="font-medium line-clamp-1">{item.product.name}</p>
                            <p className="text-sm text-gray-500">Qty: {item.quantity}</p>
                          </div>
                          <div className="ml-auto">
                            ${((item.product.sale_price || item.product.price) * item.quantity).toFixed(2)}
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
                    onClick={() => setCurrentStep('shipping')}
                  >
                    Back to Shipping
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
                <span>{shippingMethod === 'pickup' ? 'FREE' : 'Arranged by client'}</span>
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
