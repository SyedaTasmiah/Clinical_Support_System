-- Admin table setup for HMS system
-- Run this script to create the admin table if it doesn't exist

-- Create admin table if it doesn't exist
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert default admin user if table is empty
-- Default credentials: username: admin, password: admin123
-- Change these credentials after first login for security
INSERT IGNORE INTO `admin` (`username`, `password`) VALUES
('admin', 'admin123');

-- Note: In production, use proper password hashing (e.g., password_hash() in PHP)
-- This is just for initial setup
