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
      
      {/* Main Footer */}
      <div className="bg-[#232F3E] text-white">
        <div className="container mx-auto py-10 px-4">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
              <h3 className="font-bold mb-3">Get to Know Us</h3>
              <ul className="space-y-2 text-sm text-gray-300">
                <li><Link href="#"><a className="hover:underline">Careers</a></Link></li>
                <li><Link href="#"><a className="hover:underline">About Amazon</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Investor Relations</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Amazon Devices</a></Link></li>
              </ul>
            </div>
            
            <div>
              <h3 className="font-bold mb-3">Make Money with Us</h3>
              <ul className="space-y-2 text-sm text-gray-300">
                <li><Link href="#"><a className="hover:underline">Sell products on Amazon</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Sell on Amazon Business</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Become an Affiliate</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Advertise Your Products</a></Link></li>
              </ul>
            </div>
            
            <div>
              <h3 className="font-bold mb-3">Amazon Payment Products</h3>
              <ul className="space-y-2 text-sm text-gray-300">
                <li><Link href="#"><a className="hover:underline">Amazon Business Card</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Shop with Points</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Reload Your Balance</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Amazon Currency Converter</a></Link></li>
              </ul>
            </div>
            
            <div>
              <h3 className="font-bold mb-3">Let Us Help You</h3>
              <ul className="space-y-2 text-sm text-gray-300">
                <li><Link href="#"><a className="hover:underline">Amazon and COVID-19</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Your Account</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Your Orders</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Shipping Rates & Policies</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Returns & Replacements</a></Link></li>
                <li><Link href="#"><a className="hover:underline">Help</a></Link></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      
      {/* Bottom Footer */}
      <div className="bg-[#131921] py-6 text-center text-sm text-gray-300">
        <div className="container mx-auto">
          <div className="text-[#FF9900] text-2xl font-bold mb-3">
            <span className="italic">amazon</span>
            <span className="text-sm italic">clone</span>
          </div>
          <p>© 1996-{new Date().getFullYear()}, Amazon.com, Inc. or its affiliates</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
