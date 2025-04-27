import { useQuery } from "@tanstack/react-query";
import { Product } from "@shared/schema";
import ProductCard from "@/components/home/ProductCard";
import { Skeleton } from "@/components/ui/skeleton";
import StarRating from "@/components/ui/StarRating";

const Home = () => {
  // Fetch products
  const { data: products, isLoading } = useQuery<Product[]>({
    queryKey: ['/api/products'],
  });

  return (
    <div className="container mx-auto px-4 py-4">
      {/* Hero Banner */}
      <div className="relative mb-6">
        <div className="w-full h-[300px] bg-gradient-to-r from-blue-500 to-purple-600 rounded-md overflow-hidden">
          <img 
            src="https://images.unsplash.com/photo-1635273051936-7069a5e709a2?auto=format&fit=crop&w=1200&h=300" 
            alt="S-Oil Products Banner" 
            className="w-full h-full object-cover object-center opacity-90"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
          <div className="absolute bottom-0 left-0 p-6 text-white">
            <h1 className="text-3xl font-bold mb-2">S-Oil Premium Products</h1>
            <p className="text-xl mb-4">High-quality lubricants and automotive fluids</p>
          </div>
        </div>
      </div>

      {/* All Products */}
      <section className="mb-8" id="products">
        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-bold">S-Oil Products</h2>
        </div>
        
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          {isLoading ? (
            // Skeleton loading state
            Array(8).fill(0).map((_, index) => (
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
            // Display all products
            products?.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))
          )}
        </div>
      </section>
    </div>
  );
};

export default Home;
