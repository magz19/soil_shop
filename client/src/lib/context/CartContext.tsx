import { createContext, useState, useContext, useEffect, ReactNode } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { apiRequest } from "@/lib/queryClient";
import { CartItemWithProduct, Product } from "@shared/schema";
import { useToast } from "@/hooks/use-toast";

// Mock user ID for demonstration
const MOCK_USER_ID = 1;

interface Cart {
  id: number;
  userId: number;
  items: CartItemWithProduct[];
}

interface CartContextType {
  cart: Cart | null;
  isLoading: boolean;
  addToCart: (product: Product, quantity: number) => void;
  updateCartItemQuantity: (cartItemId: number, quantity: number) => void;
  removeCartItem: (cartItemId: number) => void;
  clearCart: () => void;
}

const CartContext = createContext<CartContextType | undefined>(undefined);

export const CartProvider = ({ children }: { children: ReactNode }) => {
  const { toast } = useToast();
  const queryClient = useQueryClient();
  
  // Fetch cart data
  const { data: cart, isLoading } = useQuery<Cart>({
    queryKey: [`/api/cart/${MOCK_USER_ID}`],
  });
  
  // Add to cart mutation
  const addToCartMutation = useMutation({
    mutationFn: async ({ productId, quantity }: { productId: number, quantity: number }) => {
      const res = await apiRequest('POST', '/api/cart/item', {
        userId: MOCK_USER_ID,
        productId,
        quantity
      });
      return res.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [`/api/cart/${MOCK_USER_ID}`] });
      toast({
        title: "Item added to cart",
        description: "The item has been added to your cart.",
      });
    },
    onError: (error) => {
      toast({
        title: "Error adding item to cart",
        description: error.message,
        variant: "destructive",
      });
    }
  });
  
  // Update cart item quantity mutation
  const updateCartItemMutation = useMutation({
    mutationFn: async ({ cartItemId, quantity }: { cartItemId: number, quantity: number }) => {
      const res = await apiRequest('PUT', `/api/cart/item/${cartItemId}`, {
        quantity
      });
      return res.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [`/api/cart/${MOCK_USER_ID}`] });
    },
    onError: (error) => {
      toast({
        title: "Error updating cart",
        description: error.message,
        variant: "destructive",
      });
    }
  });
  
  // Remove cart item mutation
  const removeCartItemMutation = useMutation({
    mutationFn: async (cartItemId: number) => {
      await apiRequest('DELETE', `/api/cart/item/${cartItemId}`);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [`/api/cart/${MOCK_USER_ID}`] });
      toast({
        title: "Item removed from cart",
        description: "The item has been removed from your cart.",
      });
    },
    onError: (error) => {
      toast({
        title: "Error removing item from cart",
        description: error.message,
        variant: "destructive",
      });
    }
  });

  // Add item to cart
  const addToCart = (product: Product, quantity: number) => {
    // Find if item already exists in cart
    const existingItem = cart?.items.find(item => item.product.id === product.id);
    
    if (existingItem) {
      // Update quantity if item already exists
      updateCartItemMutation.mutate({
        cartItemId: existingItem.id,
        quantity: existingItem.quantity + quantity
      });
    } else {
      // Add new item if it doesn't exist
      addToCartMutation.mutate({
        productId: product.id,
        quantity
      });
    }
  };
  
  // Update cart item quantity
  const updateCartItemQuantity = (cartItemId: number, quantity: number) => {
    updateCartItemMutation.mutate({ cartItemId, quantity });
  };
  
  // Remove cart item
  const removeCartItem = (cartItemId: number) => {
    removeCartItemMutation.mutate(cartItemId);
  };
  
  // Clear cart (remove all items)
  const clearCart = () => {
    if (!cart) return;
    
    // Remove all items one by one
    cart.items.forEach(item => {
      removeCartItemMutation.mutate(item.id);
    });
  };
  
  return (
    <CartContext.Provider
      value={{
        cart,
        isLoading,
        addToCart,
        updateCartItemQuantity,
        removeCartItem,
        clearCart
      }}
    >
      {children}
    </CartContext.Provider>
  );
};

// Hook to use cart context
export const useCart = () => {
  const context = useContext(CartContext);
  if (context === undefined) {
    throw new Error("useCart must be used within a CartProvider");
  }
  return context;
};
