import { CheckCircle2, TruckIcon, InfoIcon } from "lucide-react";
import { Order, OrderItemWithProduct } from "@shared/schema";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";

interface OrderTrackingProps {
  order: Order;
  orderItems: OrderItemWithProduct[];
}

const OrderTracking = ({ order, orderItems }: OrderTrackingProps) => {
  // Calculate the expected delivery date (3 days from order date)
  const orderDate = new Date(order.createdAt);
  const deliveryDate = new Date(orderDate);
  deliveryDate.setDate(orderDate.getDate() + 3);

  // Format dates
  const formatDate = (date: Date) => {
    return date.toLocaleDateString('en-US', {
      month: 'long',
      day: 'numeric',
      year: 'numeric'
    });
  };

  // Determine the current stage of delivery
  let currentStage = 0;
  if (order.status === 'shipped') currentStage = 1;
  if (order.status === 'out_for_delivery') currentStage = 2;
  if (order.status === 'delivered') currentStage = 3;

  // Stages of delivery
  const stages = [
    { label: 'Ordered', date: formatDate(orderDate), done: true },
    { label: 'Shipped', date: formatDate(new Date(orderDate.getTime() + 86400000 * 1)), done: currentStage >= 1 },
    { label: 'Out for delivery', date: formatDate(new Date(orderDate.getTime() + 86400000 * 2)), done: currentStage >= 2 },
    { label: 'Delivered', date: `Expected ${formatDate(deliveryDate)}`, done: currentStage >= 3 }
  ];

  return (
    <div className="mb-8">
      <div className="mb-6">
        <p className="text-gray-600 mb-2">Order #{order.id}</p>
        <p className="font-medium">Ordered on {formatDate(orderDate)}</p>
      </div>
      
      {/* Package Tracking */}
      <div className="mb-6">
        <div className="flex justify-between mb-4">
          <h3 className="text-lg font-bold">Package 1 of 1</h3>
          <span className="text-green-600 font-medium">
            {order.status === 'pending' && 'Processing'}
            {order.status === 'shipped' && 'Shipped'}
            {order.status === 'out_for_delivery' && 'Out for Delivery'}
            {order.status === 'delivered' && 'Delivered'}
          </span>
        </div>
        
        {/* Progress Bar */}
        <div className="relative pt-8 pb-4">
          <div className="absolute top-0 left-0 w-full h-1 bg-gray-200"></div>
          <div 
            className="absolute top-0 left-0 h-1 bg-green-500" 
            style={{ width: `${currentStage * 33.33}%` }}
          ></div>
          
          {/* Progress Steps */}
          <div className="flex justify-between">
            {stages.map((stage, index) => (
              <div key={index} className="relative flex flex-col items-center">
                <div className={`w-6 h-6 rounded-full ${stage.done ? 'bg-green-500' : 'bg-gray-300'} mb-1 flex items-center justify-center`}>
                  {stage.done ? (
                    <CheckCircle2 className="text-white text-xs" />
                  ) : (
                    index === 3 ? (
                      <TruckIcon className="text-gray-600 text-xs" />
                    ) : (
                      <span className="text-gray-600 text-xs">{index + 1}</span>
                    )
                  )}
                </div>
                <span className="text-xs font-medium text-center">{stage.label}</span>
                <span className="text-xs text-gray-500">{stage.date}</span>
              </div>
            ))}
          </div>
        </div>
        
        {order.status === 'out_for_delivery' && (
          <div className="bg-green-50 border border-green-200 rounded-md p-4 my-4">
            <div className="flex items-start">
              <InfoIcon className="text-green-600 mt-1 mr-3" />
              <div>
                <p className="font-medium">Arriving today by 8PM</p>
                <p className="text-sm text-gray-600">Your package is out for delivery and will arrive today.</p>
              </div>
            </div>
          </div>
        )}
      </div>
      
      {/* Order Items */}
      <div className="border-t pt-4">
        <h3 className="font-bold mb-4">Items in this shipment</h3>
        
        {orderItems.map((item, index) => (
          <div key={index} className="flex flex-col sm:flex-row items-start mb-4 pb-4 border-b">
            <div className="w-20 h-20 flex-shrink-0 mb-3 sm:mb-0">
              <img 
                src={item.product.imageUrl} 
                alt={item.product.name} 
                className="w-full h-full object-contain"
              />
            </div>
            
            <div className="flex-grow sm:pl-4">
              <h4 className="font-medium">{item.product.name}</h4>
              <p className="text-sm text-gray-500">Qty: {item.quantity}</p>
              <Link href={`/product/${item.product.id}`}>
                <Button variant="link" className="text-[#007185] hover:text-[#C7511F] p-0 h-auto text-sm mt-2">
                  Buy again
                </Button>
              </Link>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default OrderTracking;
