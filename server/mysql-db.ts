import * as mysql from 'mysql2/promise';
import { drizzle } from 'drizzle-orm/mysql2';
import * as schema from "@shared/schema";

// I'm creating a connection to either a local XAMPP MySQL server 
// or we can use the PostgreSQL DB that's already set up

let connection: mysql.Pool | null = null;
let mysqlDb: ReturnType<typeof drizzle> | null = null;

// Check if we have XAMPP MySQL connection details from the user
// If not, we'll fall back to the PostgreSQL database
// This lets us easily switch when the user provides XAMPP details
if (process.env.MYSQL_HOST) {
  console.log("Using MySQL/XAMPP connection");
  try {
    connection = mysql.createPool({
      host: process.env.MYSQL_HOST === 'local' ? 'localhost' : process.env.MYSQL_HOST,
      port: parseInt(process.env.MYSQL_PORT || '3306'),
      user: process.env.MYSQL_USER,
      password: process.env.MYSQL_PASSWORD,
      database: process.env.MYSQL_DATABASE,
      // Add connection timeout for faster error detection
      connectTimeout: 5000,
      // Only try to connect once (fast fail)
      connectionLimit: 1
    });
    
    // Initialize drizzle with MySQL
    mysqlDb = drizzle(connection, { schema, mode: "default" });
    
    // Log connection status but don't wait for it to complete
    connection.query('SELECT 1')
      .then(() => console.log("MySQL connection test successful!"))
      .catch(err => {
        console.log("MySQL connection test failed:", err.message);
        console.log("Falling back to PostgreSQL - MySQL connection settings provided but server not accessible");
        connection = null;
        mysqlDb = null;
      });
  } catch (err: any) {
    console.log("Error setting up MySQL connection:", err.message);
    console.log("Falling back to PostgreSQL database");
    connection = null;
    mysqlDb = null;
  }
} else {
  console.log("MySQL connection details not found. Using PostgreSQL instead.");
  // We'll continue using the PostgreSQL database for now
  // This avoids breaking changes until XAMPP MySQL details are provided
}

// Export the MySQL db if it's available
export { mysqlDb };

// Helper to check if MySQL is configured
export function isMysqlConfigured(): boolean {
  return !!connection;
}