import type { Express } from "express";
import { createServer, type Server } from "http";
import { storage } from "./storage";

export async function registerRoutes(app: Express): Promise<Server> {
  // API Routes
  
  // Get all products
  app.get("/api/products", async (req, res) => {
    try {
      const products = await storage.getAllProducts();
      res.json(products);
    } catch (error) {
      res.status(500).json({ message: "Error getting products" });
    }
  });

  // Get product by ID
  app.get("/api/products/:id", async (req, res) => {
    try {
      const product = await storage.getProduct(parseInt(req.params.id));
      if (!product) {
        return res.status(404).json({ message: "Product not found" });
      }
      res.json(product);
    } catch (error) {
      res.status(500).json({ message: "Error getting product" });
    }
  });

  // Get products by category
  app.get("/api/products/category/:category", async (req, res) => {
    try {
      const products = await storage.getProductsByCategory(req.params.category);
      res.json(products);
    } catch (error) {
      res.status(500).json({ message: "Error getting products by category" });
    }
  });

  // Get cart for user
  app.get("/api/cart/:userId", async (req, res) => {
    try {
      const cart = await storage.getCartWithProducts(parseInt(req.params.userId));
      res.json(cart || { id: 0, userId: parseInt(req.params.userId), items: [] });
    } catch (error) {
      res.status(500).json({ message: "Error getting cart" });
    }
  });

  // Add item to cart
  app.post("/api/cart/item", async (req, res) => {
    try {
      const { userId, productId, quantity } = req.body;
      const cartItem = await storage.addToCart(parseInt(userId), parseInt(productId), parseInt(quantity));
      res.status(201).json(cartItem);
    } catch (error) {
      res.status(500).json({ message: "Error adding item to cart" });
    }
  });

  // Update cart item quantity
  app.put("/api/cart/item/:id", async (req, res) => {
    try {
      const { quantity } = req.body;
      const updatedItem = await storage.updateCartItemQuantity(parseInt(req.params.id), parseInt(quantity));
      res.json(updatedItem);
    } catch (error) {
      res.status(500).json({ message: "Error updating cart item" });
    }
  });

  // Remove item from cart
  app.delete("/api/cart/item/:id", async (req, res) => {
    try {
      await storage.removeCartItem(parseInt(req.params.id));
      res.status(204).end();
    } catch (error) {
      res.status(500).json({ message: "Error removing item from cart" });
    }
  });

  // Create order
  app.post("/api/orders", async (req, res) => {
    try {
      const orderData = req.body;
      const newOrder = await storage.createOrder(orderData);
      res.status(201).json(newOrder);
    } catch (error) {
      res.status(500).json({ message: "Error creating order" });
    }
  });

  // Get user orders
  app.get("/api/orders/user/:userId", async (req, res) => {
    try {
      const orders = await storage.getUserOrders(parseInt(req.params.userId));
      res.json(orders);
    } catch (error) {
      res.status(500).json({ message: "Error getting user orders" });
    }
  });

  // Get order by ID
  app.get("/api/orders/:id", async (req, res) => {
    try {
      const order = await storage.getOrderWithItems(parseInt(req.params.id));
      if (!order) {
        return res.status(404).json({ message: "Order not found" });
      }
      res.json(order);
    } catch (error) {
      res.status(500).json({ message: "Error getting order" });
    }
  });

  // ADMIN ROUTES
  
  // Get all orders for admin dashboard
  app.get("/api/admin/orders", async (req, res) => {
    try {
      const orders = await storage.getAllOrders();
      res.json(orders);
    } catch (error) {
      res.status(500).json({ message: "Error getting all orders" });
    }
  });
  
  // Update order status
  app.put("/api/admin/orders/:id/status", async (req, res) => {
    try {
      const { status } = req.body;
      const order = await storage.updateOrderStatus(parseInt(req.params.id), status);
      if (!order) {
        return res.status(404).json({ message: "Order not found" });
      }
      res.json(order);
    } catch (error) {
      res.status(500).json({ message: "Error updating order status" });
    }
  });
  
  // Get order details with items
  app.get("/api/admin/orders/:id", async (req, res) => {
    try {
      const order = await storage.getOrderWithItems(parseInt(req.params.id));
      if (!order) {
        return res.status(404).json({ message: "Order not found" });
      }
      res.json(order);
    } catch (error) {
      res.status(500).json({ message: "Error getting order details" });
    }
  });

  const httpServer = createServer(app);
  return httpServer;
}
