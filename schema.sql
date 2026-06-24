CREATE DATABASE IF NOT EXISTS barangay_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barangay_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'resident') DEFAULT 'resident',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email)
);

-- Officials Table
CREATE TABLE IF NOT EXISTS officials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  position VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_name (full_name)
);

-- Clearances Table
CREATE TABLE IF NOT EXISTS clearances (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  purpose VARCHAR(255) NOT NULL,
  status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_id (user_id),
  INDEX idx_status (status)
);

-- Residents Table (Registry of Inhabitants)
CREATE TABLE IF NOT EXISTS residents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  last_name VARCHAR(100) NOT NULL,
  first_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100) DEFAULT NULL,
  sex ENUM('Male', 'Female') NOT NULL,
  birthdate DATE NOT NULL,
  civil_status ENUM('Single', 'Married', 'Widowed', 'Divorced', 'Separated') DEFAULT 'Single',
  household_id VARCHAR(50) DEFAULT NULL,
  is_pwd TINYINT(1) NOT NULL DEFAULT 0,
  is_solo_parent TINYINT(1) NOT NULL DEFAULT 0,
  is_osy TINYINT(1) NOT NULL DEFAULT 0,
  created_by INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_household (household_id),
  INDEX idx_name (last_name, first_name)
);

-- Insert Sample Officials
INSERT INTO officials (full_name, position) VALUES
('Sergio Tesorio', 'Punong Barangay'),
('Junior Hora', 'Kagawad - Peace & Order'),
('Allan Bolivar', 'Kagawad - Health'),
('Eleazar Dano', 'Kagawad - Infrastructure'),
('Ondoy Orehuela', 'Kagawad - Environment'),
('Charisse Pena', 'Secretary'),
('Jocelyn Langbid', 'Treasurer');
