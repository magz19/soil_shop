import { Link, useLocation } from "wouter";
import { ShoppingCart } from "lucide-react";
import { useCart } from "@/lib/context/CartContext";

const Header = () => {
  const [location, setLocation] = useLocation();
  const { cart } = useCart();

  const itemCount = cart?.items?.reduce((total, item) => total + item.quantity, 0) || 0;

  // Check if current path is admin dashboard
  const isAdmin = location.startsWith('/admin');

  return (
    <header className="w-full">
      {/* Top Navigation Bar */}
      <div className="bg-[#131921] text-white py-2 px-2 md:px-4">
        <div className="container mx-auto flex items-center justify-between">
          {/* Logo */}
          <div className="flex items-center space-x-4">
            <Link href="/">
              <a className="flex items-center">
                <div className="text-[#FF9900] text-2xl font-bold">
                  <span className="font-bold">S-Oil</span>
                  <span className="text-sm">Products</span>
                </div>
              </a>
            </Link>
          </div>
          
          {/* Right Nav */}
          <div className="flex items-center space-x-4 text-sm">
            {/* Admin Dashboard Link (only visible on customer pages) */}
            {!isAdmin && (
              <Link href="/admin/dashboard">
                <span className="text-white hover:text-[#FF9900] cursor-pointer">
                  <span className="font-bold">Admin</span>
                </span>
              </Link>
            )}
            
            {/* Cart Link (only visible on customer pages) */}
            {!isAdmin && (
              <Link href="/cart">
                <span className="flex items-end cursor-pointer">
                  <div className="relative">
                    <ShoppingCart className="h-6 w-6" />
                    <span className="absolute -top-1 -right-1 bg-[#FF9900] text-black rounded-full h-5 w-5 flex items-center justify-center text-xs font-bold">
                      {itemCount}
                    </span>
                  </div>
                  <span className="hidden sm:inline font-bold ml-1">Cart</span>
                </span>
              </Link>
            )}
            
            {/* Home Link (only visible on admin pages) */}
            {isAdmin && (
              <Link href="/">
                <span className="text-white hover:text-[#FF9900] cursor-pointer">
                  <span className="font-bold">Store Front</span>
                </span>
              </Link>
            )}
          </div>
        </div>
      </div>
      
      {/* Secondary Nav with Credits */}
      <div className="bg-[#232F3E] py-1 px-2">
        <div className="container mx-auto flex items-center justify-between text-white text-sm">
          <span className="text-xs italic text-gray-300">© 2025 S-Oil Products Store</span>
        </div>
      </div>
    </header>
  );
};

export default Header;
