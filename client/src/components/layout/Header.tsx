import { Link, useLocation } from "wouter";
import { useState } from "react";
import { 
  Search, 
  ShoppingCart, 
  Menu, 
  MapPin, 
  ChevronDown, 
  Globe 
} from "lucide-react";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { useCart } from "@/lib/context/CartContext";

const Header = () => {
  const [location, setLocation] = useLocation();
  const { cart } = useCart();
  const [searchQuery, setSearchQuery] = useState("");
  const [searchCategory, setSearchCategory] = useState("All");

  const itemCount = cart?.items?.reduce((total, item) => total + item.quantity, 0) || 0;

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    // In a real app, this would navigate to search results
    console.log(`Searching for ${searchQuery} in ${searchCategory}`);
  };

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
                  <span className="italic">amazon</span>
                  <span className="text-sm italic">clone</span>
                </div>
              </a>
            </Link>
            
            {/* Location */}
            <div className="hidden sm:flex items-center space-x-1 text-sm">
              <MapPin size={16} />
              <div>
                <div className="text-gray-300 text-xs">Deliver to</div>
                <div className="font-bold">United States</div>
              </div>
            </div>
          </div>
          
          {/* Search Bar */}
          <form onSubmit={handleSearch} className="flex-grow mx-2 md:mx-4 max-w-3xl">
            <div className="flex">
              <select 
                className="bg-gray-100 text-black text-sm px-2 rounded-l-md border-r border-gray-300"
                value={searchCategory}
                onChange={(e) => setSearchCategory(e.target.value)}
              >
                <option>All</option>
                <option>Electronics</option>
                <option>Home & Kitchen</option>
                <option>Fashion</option>
                <option>Books</option>
              </select>
              <Input
                type="text"
                placeholder="Search Amazon"
                className="w-full py-1 rounded-none border-0"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
              />
              <Button 
                type="submit" 
                className="bg-[#FF9900] hover:bg-[#F0AD4E] text-black rounded-l-none rounded-r-md px-3"
              >
                <Search className="h-5 w-5" />
              </Button>
            </div>
          </form>
          
          {/* Right Nav */}
          <div className="flex items-center space-x-4 text-sm">
            {/* Language */}
            <div className="hidden md:flex items-center space-x-1">
              <Globe className="h-4 w-4" />
              <span>EN</span>
              <ChevronDown className="h-3 w-3 text-gray-400" />
            </div>
            
            {/* Account */}
            <div className="hidden sm:block">
              <Link href="/account">
                <a>
                  <div className="text-xs">Hello, Sign in</div>
                  <div className="font-bold flex items-center">
                    Account & Lists
                    <ChevronDown className="h-3 w-3 text-gray-400 ml-1" />
                  </div>
                </a>
              </Link>
            </div>
            
            {/* Orders */}
            <div className="hidden md:block">
              <Link href="/orders">
                <a>
                  <div className="text-xs">Returns</div>
                  <div className="font-bold">& Orders</div>
                </a>
              </Link>
            </div>
            
            {/* Cart */}
            <Link href="/cart">
              <a className="flex items-end">
                <div className="relative">
                  <ShoppingCart className="h-6 w-6" />
                  <span className="absolute -top-1 -right-1 bg-[#FF9900] text-black rounded-full h-5 w-5 flex items-center justify-center text-xs font-bold">
                    {itemCount}
                  </span>
                </div>
                <span className="hidden sm:inline font-bold ml-1">Cart</span>
              </a>
            </Link>
          </div>
        </div>
      </div>
      
      {/* Secondary Nav */}
      <div className="bg-[#232F3E] py-1 px-2">
        <div className="container mx-auto flex items-center text-white text-sm space-x-3 overflow-x-auto">
          <Link href="/all">
            <a className="flex items-center px-2 py-1 hover:border hover:border-white rounded">
              <Menu className="mr-1 h-4 w-4" /> All
            </a>
          </Link>
          <Link href="/deals">
            <a className="px-2 py-1 hover:border hover:border-white rounded whitespace-nowrap">
              Today's Deals
            </a>
          </Link>
          <Link href="/customer-service">
            <a className="px-2 py-1 hover:border hover:border-white rounded whitespace-nowrap">
              Customer Service
            </a>
          </Link>
          <Link href="/registry">
            <a className="px-2 py-1 hover:border hover:border-white rounded whitespace-nowrap">
              Registry
            </a>
          </Link>
          <Link href="/gift-cards">
            <a className="px-2 py-1 hover:border hover:border-white rounded whitespace-nowrap">
              Gift Cards
            </a>
          </Link>
          <Link href="/sell">
            <a className="px-2 py-1 hover:border hover:border-white rounded whitespace-nowrap">
              Sell
            </a>
          </Link>
          <Link href="/electronics">
            <a className="px-2 py-1 text-[#FF9900] font-bold hover:border hover:border-white rounded ml-auto whitespace-nowrap">
              Shop deals in Electronics
            </a>
          </Link>
        </div>
      </div>
    </header>
  );
};

export default Header;
