import { useQuery } from "@tanstack/react-query";
import { useParams } from "wouter";
import { Order, OrderItemWithProduct } from "@shared/schema";
import OrderTracking from "@/components/orders/OrderTracking";
import { Skeleton } from "@/components/ui/skeleton";

const OrderTrackingPage = () => {
  const { id } = useParams();
  
  // Fetch order details
  const { data: order, isLoading: isOrderLoading } = useQuery<Order & { items: OrderItemWithProduct[] }>({
    queryKey: [`/api/orders/${id}`],
  });

  if (isOrderLoading) {
    return (
      <div className="container mx-auto px-4 py-8">
        <div className="bg-white rounded-md shadow p-6">
          <Skeleton className="h-6 w-1/3 mb-2" />
          <Skeleton className="h-4 w-1/4 mb-6" />
          
          <Skeleton className="h-6 w-1/3 mb-3" />
          <Skeleton className="h-4 w-full mb-4" />
          
          <div className="relative pt-8 pb-4 mb-6">
            <Skeleton className="h-1 w-full mb-4" />
            <div className="flex justify-between">
              {[1, 2, 3, 4].map((i) => (
                <div key={i} className="flex flex-col items-center">
                  <Skeleton className="h-6 w-6 rounded-full mb-2" />
                  <Skeleton className="h-3 w-16 mb-1" />
                  <Skeleton className="h-3 w-12" />
                </div>
              ))}
            </div>
          </div>
          
          <Skeleton className="h-6 w-1/4 mb-4" />
          
          {[1, 2].map((i) => (
            <div key={i} className="flex mb-4 pb-4 border-b">
              <Skeleton className="h-20 w-20 mr-4" />
              <div className="flex-grow">
                <Skeleton className="h-4 w-3/4 mb-2" />
                <Skeleton className="h-3 w-1/4 mb-2" />
                <Skeleton className="h-3 w-1/6" />
              </div>
            </div>
          ))}
        </div>
      </div>
    );
  }

  if (!order) {
    return (
      <div className="container mx-auto px-4 py-8 text-center">
        <h2 className="text-2xl font-bold mb-4">Order Not Found</h2>
        <p>We couldn't find an order with the ID: {id}</p>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold mb-6">Track Your Order</h1>
      
      <div className="bg-white rounded-md shadow p-6">
        <OrderTracking order={order} orderItems={order.items} />
      </div>
    </div>
  );
};

export default OrderTrackingPage;
