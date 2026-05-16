CREATE DATABASE IF NOT EXISTS fahrzeugkosten_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fahrzeugkosten_tracker;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('anonymous', 'registered', 'administrator') NOT NULL DEFAULT 'registered',
    status ENUM('active', 'deactivated') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Vehicles Table
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    license_plate VARCHAR(20) NOT NULL,
    registration_date DATE NOT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Cost Entries Table (Base)
CREATE TABLE IF NOT EXISTS cost_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    date DATE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    mileage INT NOT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Refuel Entries Table (Extension)
CREATE TABLE IF NOT EXISTS refuel_entries (
    entry_id INT PRIMARY KEY,
    liters DECIMAL(10, 2) NOT NULL,
    price_per_liter DECIMAL(10, 3) NOT NULL,
    FOREIGN KEY (entry_id) REFERENCES cost_entries(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Entry to Category M:N
CREATE TABLE IF NOT EXISTS entry_category (
    entry_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (entry_id, category_id),
    FOREIGN KEY (entry_id) REFERENCES cost_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Audit Logs Table
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    ip_address VARCHAR(45) NOT NULL,
    action TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Seed Data
INSERT INTO categories (name) VALUES ('Tanken'), ('Werkstatt'), ('Versicherung'), ('Steuer'), ('Sonstiges');

-- Initial Admin User (Password: admin123)
INSERT INTO users (firstname, lastname, email, password_hash, role) 
VALUES ('Admin', 'User', 'admin@example.com', '$2y$10$mPWMfz7.T3VzEic/YHldVuG8tyqolmHZL.s5at3BdPT9lqSiWKCGm', 'administrator');

-- Initial Registered User (Password: user123)
INSERT INTO users (firstname, lastname, email, password_hash, role) 
VALUES ('John', 'Doe', 'john@example.com', '$2y$10$2he/vNHqJL49Y57Vq0zDcuMz6Bq00m57zaVaDvbHrVnGGsTg5S32y', 'registered');

-- Test Data: Vehicles
INSERT INTO vehicles (user_id, name, license_plate, registration_date)
VALUES (2, 'VW Golf', 'W-12345B', '2020-05-15');

-- Test Data: Cost Entries
INSERT INTO cost_entries (vehicle_id, date, amount, description, mileage)
VALUES (1, '2026-05-01', 65.50, 'Vollgetankt', 15000);

-- Test Data: Refuel Details
INSERT INTO refuel_entries (entry_id, liters, price_per_liter)
VALUES (1, 40.5, 1.617);

-- Test Data: Entry Categories
INSERT INTO entry_category (entry_id, category_id)
VALUES (1, 1);
