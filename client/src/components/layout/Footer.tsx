import { Link } from "wouter";
import { ArrowUp } from "lucide-react";

const Footer = () => {
  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth"
    });
  };

  return (
    <footer className="mt-12">
      {/* Back to top */}
      <div className="bg-[#37475A] hover:bg-[#485769] cursor-pointer">
        <div className="container mx-auto py-3 text-center text-white" onClick={scrollToTop}>
          <div className="flex items-center justify-center">
            <ArrowUp className="mr-2 h-4 w-4" />
            <span>Back to top</span>
          </div>
        </div>
      </div>
      
      {/* Bottom Footer - Only Credits */}
      <div className="bg-[#131921] py-6 text-center text-sm text-gray-300">
        <div className="container mx-auto">
          <div className="text-[#FF9900] text-2xl font-bold mb-3">
            <span className="font-bold">S-Oil</span>
            <span className="text-sm">Products</span>
          </div>
          <p>© {new Date().getFullYear()} S-Oil Products Store</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
