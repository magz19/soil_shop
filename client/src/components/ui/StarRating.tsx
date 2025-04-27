import { Star, StarHalf } from "lucide-react";

interface StarRatingProps {
  rating: number;
  size?: 'sm' | 'md' | 'lg';
}

const StarRating = ({ rating, size = 'md' }: StarRatingProps) => {
  // Round to nearest half
  const roundedRating = Math.round(rating * 2) / 2;
  
  // Create array of 5 stars
  const stars = [];
  
  // Size classes
  const sizeClasses = {
    sm: 'h-3 w-3',
    md: 'h-4 w-4',
    lg: 'h-5 w-5'
  };
  
  // Fill the stars array
  for (let i = 1; i <= 5; i++) {
    if (i <= roundedRating) {
      // Full star
      stars.push(
        <Star 
          key={i} 
          className={`${sizeClasses[size]} text-[#FF9900] fill-[#FF9900]`} 
        />
      );
    } else if (i - 0.5 === roundedRating) {
      // Half star
      stars.push(
        <StarHalf 
          key={i} 
          className={`${sizeClasses[size]} text-[#FF9900] fill-[#FF9900]`}
        />
      );
    } else {
      // Empty star
      stars.push(
        <Star 
          key={i} 
          className={`${sizeClasses[size]} text-[#FF9900]`} 
        />
      );
    }
  }
  
  return (
    <div className="flex">
      {stars}
    </div>
  );
};

export default StarRating;
