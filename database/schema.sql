CREATE TABLE IF NOT EXISTS tours (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE,
  destination VARCHAR(120) NOT NULL,
  category VARCHAR(120) NOT NULL,
  duration VARCHAR(60) NOT NULL,
  starting_price DECIMAL(10,2) NOT NULL,
  itinerary JSON,
  inclusions JSON,
  exclusions JSON,
  gallery JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tour_id INT NULL,
  guest_name VARCHAR(120) NOT NULL,
  guest_email VARCHAR(190) NOT NULL,
  rating TINYINT NOT NULL,
  review_text TEXT NOT NULL,
  photo_path VARCHAR(255) NULL,
  source ENUM('local','google','tripadvisor') DEFAULT 'local',
  approved TINYINT(1) DEFAULT 0,
  helpful_votes INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE SET NULL,
  INDEX idx_reviews_approved (approved),
  INDEX idx_reviews_email (guest_email)
);

CREATE TABLE IF NOT EXISTS blogs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE,
  category VARCHAR(100) NOT NULL,
  content LONGTEXT NOT NULL,
  featured_image VARCHAR(255),
  author_id INT,
  published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(190) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  password_reset_token VARCHAR(255) DEFAULT NULL,
  password_reset_expires DATETIME DEFAULT NULL,
  failed_login_attempts INT DEFAULT 0,
  account_locked_until DATETIME DEFAULT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tour_id INT NULL,
  customer_name VARCHAR(120) NOT NULL,
  customer_email VARCHAR(190) NOT NULL,
  tour_name VARCHAR(255) NOT NULL,
  guests INT NOT NULL,
  travel_date DATE,
  status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE SET NULL,
  INDEX idx_bookings_email (customer_email)
);

CREATE TABLE IF NOT EXISTS inquiries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  contact_name VARCHAR(120) NOT NULL,
  contact_email VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS languages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(10) UNIQUE NOT NULL,
  name VARCHAR(60) NOT NULL,
  enabled TINYINT(1) DEFAULT 1
);
