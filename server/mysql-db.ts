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
  connection = mysql.createPool({
    host: process.env.MYSQL_HOST,
    port: parseInt(process.env.MYSQL_PORT || '3306'),
    user: process.env.MYSQL_USER,
    password: process.env.MYSQL_PASSWORD,
    database: process.env.MYSQL_DATABASE
  });
  
  // Initialize drizzle with MySQL
  mysqlDb = drizzle(connection, { schema, mode: "default" });
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