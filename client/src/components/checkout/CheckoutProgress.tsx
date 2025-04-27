interface CheckoutProgressProps {
  currentStep: 'shipping' | 'payment' | 'review';
}

const CheckoutProgress = ({ currentStep }: CheckoutProgressProps) => {
  const steps = [
    { id: 'shipping', label: 'Shipping' },
    { id: 'payment', label: 'Payment' },
    { id: 'review', label: 'Review' }
  ];

  // Determine progress percentage
  const getProgressPercentage = () => {
    if (currentStep === 'shipping') return 33;
    if (currentStep === 'payment') return 66;
    return 100;
  };

  return (
    <div className="flex justify-between mb-8 relative">
      <div className="absolute top-4 left-0 h-1 bg-gray-300 w-full -z-10"></div>
      <div 
        className="absolute top-4 left-0 h-1 bg-[#146EB4] -z-5"
        style={{ width: `${getProgressPercentage()}%` }}
      ></div>
      
      {steps.map((step, index) => {
        const isActive = steps.findIndex(s => s.id === currentStep) >= index;
        
        return (
          <div key={step.id} className="flex flex-col items-center z-10">
            <div 
              className={`w-8 h-8 rounded-full flex items-center justify-center font-bold mb-2 ${
                isActive ? 'bg-[#146EB4] text-white' : 'bg-gray-300 text-gray-700'
              }`}
            >
              {index + 1}
            </div>
            <span className="text-sm font-medium">{step.label}</span>
          </div>
        );
      })}
    </div>
  );
};

export default CheckoutProgress;
