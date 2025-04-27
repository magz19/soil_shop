import { useParams, useLocation } from "wouter";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { Order, OrderItemWithProduct } from "@shared/schema";
import { useState } from "react";
import { apiRequest } from "@/lib/queryClient";
import { useToast } from "@/hooks/use-toast";
import { 
  Card, 
  CardContent, 
  CardDescription, 
  CardHeader, 
  CardTitle,
  CardFooter
} from "@/components/ui/card";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { 
  ArrowLeft, 
  Truck, 
  Package, 
  MapPin, 
  Phone, 
  User, 
  Mail,
  CreditCard
} from "lucide-react";
import { Skeleton } from "@/components/ui/skeleton";
import { Separator } from "@/components/ui/separator";

const AdminOrderDetails = () => {
  const { id } = useParams();
  const [, navigate] = useLocation();
  const { toast } = useToast();
  const queryClient = useQueryClient();
  const [status, setStatus] = useState<string>("");
  
  // Fetch order details
  const { 
    data: order, 
    isLoading 
  } = useQuery<Order & { items: OrderItemWithProduct[] }>({
    queryKey: [`/api/admin/orders/${id}`]
  });
  
  // Set status when order loads
  React.useEffect(() => {
    if (order) {
      setStatus(order.status);
    }
  }, [order]);
  
  // Update order status mutation
  const updateStatusMutation = useMutation({
    mutationFn: async (newStatus: string) => {
      const res = await apiRequest('PUT', `/api/admin/orders/${id}/status`, { status: newStatus });
      return res.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [`/api/admin/orders/${id}`] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/orders'] });
      toast({
        title: "Order status updated",
        description: "The order status has been updated successfully."
      });
    },
    onError: (error) => {
      toast({
        title: "Error updating status",
        description: error.message,
        variant: "destructive"
      });
    }
  });
  
  // Handle status change
  const handleStatusChange = (value: string) => {
    setStatus(value);
    updateStatusMutation.mutate(value);
  };
  
  // Format date
  const formatDate = (date: Date) => {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };
  
  // Get status badge color
  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'pending':
        return <Badge className="bg-yellow-500">Pending</Badge>;
      case 'processing':
        return <Badge className="bg-blue-500">Processing</Badge>;
      case 'shipped':
        return <Badge className="bg-indigo-500">Shipped</Badge>;
      case 'out_for_delivery':
        return <Badge className="bg-purple-500">Out for Delivery</Badge>;
      case 'delivered':
        return <Badge className="bg-green-500">Delivered</Badge>;
      case 'cancelled':
        return <Badge className="bg-red-500">Cancelled</Badge>;
      default:
        return <Badge className="bg-gray-500">{status}</Badge>;
    }
  };

  if (isLoading) {
    return (
      <div className="container mx-auto px-4 py-8">
        <div className="flex items-center mb-6">
          <Button variant="ghost" size="sm" className="mr-2">
            <ArrowLeft className="h-4 w-4 mr-2" />
            <Skeleton className="h-4 w-20" />
          </Button>
        </div>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="md:col-span-2 space-y-6">
            <Skeleton className="h-48 w-full" />
            <Skeleton className="h-64 w-full" />
          </div>
          <div>
            <Skeleton className="h-80 w-full" />
          </div>
        </div>
      </div>
    );
  }

  if (!order) {
    return (
      <div className="container mx-auto px-4 py-8 text-center">
        <h2 className="text-2xl font-bold mb-4">Order Not Found</h2>
        <p className="mb-4">We couldn't find an order with the ID: {id}</p>
        <Button onClick={() => navigate('/admin/dashboard')}>
          <ArrowLeft className="h-4 w-4 mr-2" />
          Back to Dashboard
        </Button>
      </div>
    );
  }
  
  // Calculate totals
  const subtotal = order.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  const shippingCost = order.shippingMethod === 'pickup' ? 0 : 
                       order.shippingMethod === 'sameDay' ? 14.99 : 
                       order.shippingMethod === 'oneDay' ? 9.99 : 0;

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="flex items-center mb-6">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/dashboard')} className="mr-2">
          <ArrowLeft className="h-4 w-4 mr-2" />
          Back to Dashboard
        </Button>
        <h1 className="text-2xl font-bold">Order #{order.id}</h1>
      </div>
      
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {/* Main Content */}
        <div className="md:col-span-2 space-y-6">
          {/* Order Info */}
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>Order Information</CardTitle>
                <CardDescription>Placed on {formatDate(order.createdAt)}</CardDescription>
              </div>
              <div>
                <Select value={status} onValueChange={handleStatusChange}>
                  <SelectTrigger className="w-40">
                    <SelectValue placeholder="Status" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="pending">Pending</SelectItem>
                    <SelectItem value="processing">Processing</SelectItem>
                    <SelectItem value="shipped">Shipped</SelectItem>
                    <SelectItem value="out_for_delivery">Out for Delivery</SelectItem>
                    <SelectItem value="delivered">Delivered</SelectItem>
                    <SelectItem value="cancelled">Cancelled</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </CardHeader>
            <CardContent>
              <div className="flex flex-col md:flex-row md:space-x-6">
                <div className="flex-1 mb-4 md:mb-0">
                  <h3 className="font-semibold text-sm text-gray-500 mb-2">Shipping Details</h3>
                  <div className="space-y-2">
                    <div className="flex items-start">
                      <MapPin className="h-4 w-4 mr-2 mt-1 text-gray-400" />
                      <div>
                        <p>{order.shippingAddress}</p>
                        <p>{order.shippingCity}, {order.shippingState} {order.shippingZip}</p>
                      </div>
                    </div>
                    <div className="flex items-center">
                      <Truck className="h-4 w-4 mr-2 text-gray-400" />
                      <span>
                        {order.shippingMethod === 'pickup' ? 'Personal Pickup' : 
                         order.shippingMethod === 'delivery' ? 'Grab/Lalamove' : 
                         order.shippingMethod}
                      </span>
                    </div>
                    {order.shippingMethod === 'pickup' && (
                      <div className="pl-6">
                        <p className="text-sm text-gray-600">Pickup Address: 123 Mendiola St. Manila City</p>
                        <p className="text-sm text-gray-600">Contact: Anjhela Geron 09454545</p>
                      </div>
                    )}
                  </div>
                </div>
                
                <div className="flex-1">
                  <h3 className="font-semibold text-sm text-gray-500 mb-2">Payment Information</h3>
                  <div className="space-y-2">
                    <div className="flex items-center">
                      <CreditCard className="h-4 w-4 mr-2 text-gray-400" />
                      <span>GCash Payment</span>
                    </div>
                    <div className="pl-6">
                      <p className="text-sm text-gray-600">Martin Magno 091234567</p>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
          
          {/* Order Items */}
          <Card>
            <CardHeader>
              <CardTitle>Order Items</CardTitle>
              <CardDescription>
                {order.items.reduce((sum, item) => sum + item.quantity, 0)} items
              </CardDescription>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Product</TableHead>
                    <TableHead>Price</TableHead>
                    <TableHead>Quantity</TableHead>
                    <TableHead className="text-right">Total</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {order.items.map((item) => (
                    <TableRow key={item.id}>
                      <TableCell>
                        <div className="flex items-center">
                          <div className="w-12 h-12 mr-3 flex-shrink-0">
                            <img 
                              src={item.product.imageUrl}
                              alt={item.product.name}
                              className="w-full h-full object-contain"
                            />
                          </div>
                          <div>
                            <p className="font-medium">{item.product.name}</p>
                            <p className="text-sm text-gray-500">{item.product.category}</p>
                          </div>
                        </div>
                      </TableCell>
                      <TableCell>${item.price.toFixed(2)}</TableCell>
                      <TableCell>{item.quantity}</TableCell>
                      <TableCell className="text-right">${(item.price * item.quantity).toFixed(2)}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </div>
        
        {/* Order Summary */}
        <div>
          <Card className="sticky top-4">
            <CardHeader>
              <CardTitle>Order Summary</CardTitle>
              <div className="flex items-center mt-2">
                <Badge className="mr-2" variant="outline">Status</Badge>
                {getStatusBadge(order.status)}
              </div>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <div>
                  <h3 className="font-semibold text-sm text-gray-500 mb-2">Customer Information</h3>
                  <div className="space-y-2">
                    <div className="flex items-center">
                      <User className="h-4 w-4 mr-2 text-gray-400" />
                      <span>User #{order.userId}</span>
                    </div>
                  </div>
                </div>
                
                <Separator />
                
                <div>
                  <h3 className="font-semibold text-sm text-gray-500 mb-2">Order Totals</h3>
                  <div className="space-y-2">
                    <div className="flex justify-between">
                      <span className="text-gray-600">Subtotal:</span>
                      <span>${subtotal.toFixed(2)}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-600">Shipping:</span>
                      <span>
                        {shippingCost === 0 ? 'FREE' : `$${shippingCost.toFixed(2)}`}
                      </span>
                    </div>
                    <Separator />
                    <div className="flex justify-between font-bold">
                      <span>Total:</span>
                      <span>${order.total.toFixed(2)}</span>
                    </div>
                  </div>
                </div>
              </div>
            </CardContent>
            <CardFooter className="flex flex-col space-y-2">
              <Button 
                className="w-full"
                disabled={updateStatusMutation.isPending}
                onClick={() => {
                  const nextStatus = {
                    'pending': 'processing',
                    'processing': 'shipped',
                    'shipped': 'out_for_delivery',
                    'out_for_delivery': 'delivered'
                  }[order.status];
                  
                  if (nextStatus) {
                    handleStatusChange(nextStatus);
                  }
                }}
              >
                {updateStatusMutation.isPending ? 'Updating...' : `Move to ${
                  {
                    'pending': 'Processing',
                    'processing': 'Shipped',
                    'shipped': 'Out for Delivery',
                    'out_for_delivery': 'Delivered'
                  }[order.status] || 'Next Status'
                }`}
              </Button>
              {order.status !== 'cancelled' && (
                <Button 
                  variant="outline" 
                  className="w-full"
                  disabled={updateStatusMutation.isPending}
                  onClick={() => handleStatusChange('cancelled')}
                >
                  Cancel Order
                </Button>
              )}
            </CardFooter>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default AdminOrderDetails;