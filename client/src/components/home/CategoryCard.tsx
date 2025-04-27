import { Link } from "wouter";

interface CategoryCardProps {
  title: string;
  imageUrl: string;
  link: string;
}

const CategoryCard = ({ title, imageUrl, link }: CategoryCardProps) => {
  return (
    <Link href={link}>
      <a className="bg-white rounded-md shadow hover:shadow-md overflow-hidden">
        <div className="p-4">
          <h3 className="font-bold mb-2">{title}</h3>
          <div className="h-32 bg-gray-200 rounded-md overflow-hidden">
            <img 
              src={imageUrl} 
              alt={title} 
              className="w-full h-full object-cover"
            />
          </div>
          <p className="mt-2 text-[#007185] text-sm">See more</p>
        </div>
      </a>
    </Link>
  );
};

export default CategoryCard;
