import { useQuery } from "@tanstack/react-query";
import { Product } from "@shared/schema";
import CategoryCard from "@/components/home/CategoryCard";
import ProductCard from "@/components/home/ProductCard";
import { Skeleton } from "@/components/ui/skeleton";

const Home = () => {
  // Fetch products
  const { data: products, isLoading } = useQuery<Product[]>({
    queryKey: ['/api/products'],
  });

  // Categories data
  const categories = [
    {
      title: "Electronics",
      imageUrl: "https://images.unsplash.com/photo-1588508065123-287b28e013da?auto=format&fit=crop&w=300&h=200",
      link: "/category/electronics"
    },
    {
      title: "Home & Kitchen",
      imageUrl: "https://images.unsplash.com/photo-1583845112203-29329902332e?auto=format&fit=crop&w=300&h=200",
      link: "/category/home-kitchen"
    },
    {
      title: "Fashion",
      imageUrl: "https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?auto=format&fit=crop&w=300&h=200",
      link: "/category/fashion"
    },
    {
      title: "Books",
      imageUrl: "https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=300&h=200",
      link: "/category/books"
    }
  ];

  // Get recently viewed products - subset of all products
  const recentlyViewedProducts = products ? products.slice(0, 4) : [];

  return (
    <div className="container mx-auto px-4 py-4">
      {/* Hero Banner */}
      <div className="relative mb-6">
        <div className="w-full h-[300px] bg-gradient-to-r from-blue-500 to-purple-600 rounded-md overflow-hidden">
          <img 
            src="https://images.unsplash.com/photo-1607082349566-187342175e2f?auto=format&fit=crop&w=1200&h=300" 
            alt="Sale banner" 
            className="w-full h-full object-cover object-center opacity-90"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
          <div className="absolute bottom-0 left-0 p-6 text-white">
            <h1 className="text-3xl font-bold mb-2">Holiday Deals</h1>
            <p className="text-xl mb-4">Save up to 40% on electronics and home goods</p>
            <button className="bg-[#FF9900] hover:bg-[#F0AD4E] text-black font-bold py-2 px-6 rounded">
              Shop Now
            </button>
          </div>
        </div>
      </div>

      {/* Featured Categories */}
      <section className="mb-8">
        <h2 className="text-xl font-bold mb-4">Shop by Category</h2>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {categories.map((category, index) => (
            <CategoryCard 
              key={index}
              title={category.title}
              imageUrl={category.imageUrl}
              link={category.link}
            />
          ))}
        </div>
      </section>

      {/* Featured Products */}
      <section className="mb-8" id="products">
        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-bold">Today's Deals</h2>
          <a href="#" className="text-[#007185] hover:underline">See all deals</a>
        </div>
        
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          {isLoading ? (
            // Skeleton loading state
            Array(4).fill(0).map((_, index) => (
              <div key={index} className="bg-white rounded-md shadow p-4">
                <Skeleton className="h-48 w-full mb-3" />
                <Skeleton className="h-4 w-3/4 mb-2" />
                <Skeleton className="h-4 w-1/2 mb-4" />
                <Skeleton className="h-6 w-1/4 mb-2" />
                <Skeleton className="h-4 w-full mb-3" />
                <div className="flex space-x-2">
                  <Skeleton className="h-8 w-full" />
                  <Skeleton className="h-8 w-full" />
                </div>
              </div>
            ))
          ) : (
            // Display products
            products?.slice(0, 4).map((product) => (
              <ProductCard key={product.id} product={product} />
            ))
          )}
        </div>
      </section>

      {/* Recently Viewed */}
      <section className="mb-8">
        <h2 className="text-xl font-bold mb-4">Recently Viewed</h2>
        <div className="flex overflow-x-auto space-x-4 pb-4">
          {recentlyViewedProducts.map((product) => (
            <div key={product.id} className="bg-white rounded-md shadow p-3 min-w-[180px] max-w-[180px]">
              <div className="h-32 flex items-center justify-center mb-2">
                <img 
                  src={product.imageUrl} 
                  alt={product.name} 
                  className="max-h-full max-w-full object-contain"
                />
              </div>
              <h3 className="font-medium text-xs line-clamp-2 h-8">{product.name}</h3>
              <div className="flex items-center mt-1">
                <div className="text-xs">
                  <StarRating rating={product.rating || 0} size="sm" />
                </div>
                <span className="text-xs text-gray-500 ml-1">{product.reviewCount}</span>
              </div>
              <div className="mt-1">
                <span className="text-xs font-bold">${(product.salePrice || product.price).toFixed(2)}</span>
              </div>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
};

export default Home;
