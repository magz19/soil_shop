import type { Express } from "express";
import { createServer, type Server } from "http";
import { storage, DatabaseStorage } from "./storage";
import { db } from "./db";
import { mysqlDb, isMysqlConfigured } from "./mysql-db";

export async function registerRoutes(app: Express): Promise<Server> {
  // Initialize database and seed products
  if (storage instanceof DatabaseStorage) {
    try {
      console.log("Initializing database...");
      
      if (isMysqlConfigured() && mysqlDb) {
        // MySQL is configured - use it
        console.log("Using MySQL Database (XAMPP)");
        try {
          // Create tables in MySQL
          await mysqlDb.execute(`
            CREATE TABLE IF NOT EXISTS users (
              id INT AUTO_INCREMENT PRIMARY KEY,
              username VARCHAR(255) NOT NULL UNIQUE,
              password VARCHAR(255) NOT NULL,
              email VARCHAR(255) NOT NULL UNIQUE,
              full_name VARCHAR(255) NOT NULL,
              address VARCHAR(255),
              city VARCHAR(255),
              state VARCHAR(255),
              zip_code VARCHAR(20),
              phone VARCHAR(20)
            );
            
            CREATE TABLE IF NOT EXISTS products (
              id INT AUTO_INCREMENT PRIMARY KEY,
              name VARCHAR(255) NOT NULL,
              description TEXT NOT NULL,
              price DECIMAL(10, 2) NOT NULL,
              sale_price DECIMAL(10, 2),
              image_url VARCHAR(255) NOT NULL,
              category VARCHAR(100) NOT NULL,
              in_stock BOOLEAN NOT NULL DEFAULT TRUE,
              rating DECIMAL(3, 1),
              review_count INT,
              is_prime BOOLEAN NOT NULL DEFAULT FALSE
            );
            
            CREATE TABLE IF NOT EXISTS carts (
              id INT AUTO_INCREMENT PRIMARY KEY,
              user_id INT NOT NULL,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
            
            CREATE TABLE IF NOT EXISTS cart_items (
              id INT AUTO_INCREMENT PRIMARY KEY,
              cart_id INT NOT NULL,
              product_id INT NOT NULL,
              quantity INT NOT NULL
            );
            
            CREATE TABLE IF NOT EXISTS orders (
              id INT AUTO_INCREMENT PRIMARY KEY,
              user_id INT NOT NULL,
              total DECIMAL(10, 2) NOT NULL,
              status VARCHAR(50) NOT NULL DEFAULT 'pending',
              shipping_address VARCHAR(255) NOT NULL,
              shipping_city VARCHAR(100) NOT NULL,
              shipping_state VARCHAR(50) NOT NULL,
              shipping_zip VARCHAR(20) NOT NULL,
              shipping_method VARCHAR(50) NOT NULL,
              created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            );
            
            CREATE TABLE IF NOT EXISTS order_items (
              id INT AUTO_INCREMENT PRIMARY KEY,
              order_id INT NOT NULL,
              product_id INT NOT NULL,
              price DECIMAL(10, 2) NOT NULL,
              quantity INT NOT NULL
            );
          `);
          console.log("MySQL tables created successfully!");
        } catch (error) {
          console.error("Error creating MySQL tables:", error);
        }
      } else {
        // Use PostgreSQL
        console.log("Using PostgreSQL Database (default)");
        await db.execute(`CREATE TABLE IF NOT EXISTS _drizzle_migrations (
          id SERIAL PRIMARY KEY,
          hash text NOT NULL,
          created_at timestamptz DEFAULT now()
        )`);
        
        // Push schema changes to database
        console.log("Running npm run db:push equivalent...");
        try {
          await db.execute(`
            CREATE TABLE IF NOT EXISTS users (
              id SERIAL PRIMARY KEY,
              username TEXT NOT NULL UNIQUE,
              password TEXT NOT NULL,
              email TEXT NOT NULL UNIQUE,
              full_name TEXT NOT NULL,
              address TEXT,
              city TEXT,
              state TEXT,
              zip_code TEXT,
              phone TEXT
            );
            
            CREATE TABLE IF NOT EXISTS products (
              id SERIAL PRIMARY KEY,
              name TEXT NOT NULL,
              description TEXT NOT NULL,
              price DECIMAL(10, 2) NOT NULL,
              sale_price DECIMAL(10, 2),
              image_url TEXT NOT NULL,
              category TEXT NOT NULL,
              in_stock BOOLEAN NOT NULL DEFAULT true,
              rating DECIMAL(3, 1),
              review_count INTEGER,
              is_prime BOOLEAN NOT NULL DEFAULT false
            );
            
            CREATE TABLE IF NOT EXISTS carts (
              id SERIAL PRIMARY KEY,
              user_id INTEGER NOT NULL,
              created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
            );
            
            CREATE TABLE IF NOT EXISTS cart_items (
              id SERIAL PRIMARY KEY,
              cart_id INTEGER NOT NULL,
              product_id INTEGER NOT NULL,
              quantity INTEGER NOT NULL
            );
            
            CREATE TABLE IF NOT EXISTS orders (
              id SERIAL PRIMARY KEY,
              user_id INTEGER NOT NULL,
              total DECIMAL(10, 2) NOT NULL,
              status TEXT NOT NULL DEFAULT 'pending',
              shipping_address TEXT NOT NULL,
              shipping_city TEXT NOT NULL,
              shipping_state TEXT NOT NULL,
              shipping_zip TEXT NOT NULL,
              shipping_method TEXT NOT NULL,
              created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
            );
            
            CREATE TABLE IF NOT EXISTS order_items (
              id SERIAL PRIMARY KEY,
              order_id INTEGER NOT NULL,
              product_id INTEGER NOT NULL,
              price DECIMAL(10, 2) NOT NULL,
              quantity INTEGER NOT NULL
            );
          `);
          console.log("Schema tables created successfully!");
        } catch (error) {
          console.log("Some tables might already exist, continuing...");
        }
      }
      
      // Seed initial products
      await storage.seedSoilProducts();
      console.log("Database initialization complete!");
    } catch (error) {
      console.error("Database initialization error:", error);
    }
  }
  
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