-- Car Rental Agency assignment schema
-- Import this file in MySQL after creating database `car_rental_agency`

CREATE DATABASE IF NOT EXISTS car_rental_agency;
USE car_rental_agency;

DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS cars;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'agency') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE cars (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agency_id INT UNSIGNED NOT NULL,
    model VARCHAR(120) NOT NULL,
    vehicle_number VARCHAR(40) NOT NULL,
    seating_capacity INT UNSIGNED NOT NULL,
    rent_per_day DECIMAL(10, 2) NOT NULL,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cars_agency FOREIGN KEY (agency_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT uq_vehicle_number UNIQUE (vehicle_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    car_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    rental_days INT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_car FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_bookings_car (car_id),
    INDEX idx_bookings_customer (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Demo password for all seed users below: password123
INSERT INTO users (name, email, password_hash, role) VALUES
('CityDrive Rentals', 'agency1@example.com', '$2y$12$xk/t7i1Yp4bhzLHgZIqK/uB85MNh9ouJVafOrCXFBqDHQWAgUPhv6', 'agency'),
('ZoomCar Hub', 'agency2@example.com', '$2y$12$xk/t7i1Yp4bhzLHgZIqK/uB85MNh9ouJVafOrCXFBqDHQWAgUPhv6', 'agency'),
('Aarav Mehta', 'customer1@example.com', '$2y$12$xk/t7i1Yp4bhzLHgZIqK/uB85MNh9ouJVafOrCXFBqDHQWAgUPhv6', 'customer');

INSERT INTO cars (agency_id, model, vehicle_number, seating_capacity, rent_per_day, is_available) VALUES
(1, 'Toyota Innova', 'MH12AB1001', 7, 3200.00, 1),
(1, 'Hyundai i20', 'MH14CD3002', 5, 1800.00, 1),
(2, 'Maruti Swift Dzire', 'DL01XY9090', 5, 1600.00, 1);
