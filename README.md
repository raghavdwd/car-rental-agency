# Car Rental Agency - SDE Web Assignment

This project is a role-based car rental web application built with:
- Frontend: HTML + Bootstrap 5
- Backend: Core PHP (procedural + reusable includes)
- Database: MySQL

## Implemented Requirements

1. Two user types:
- Customer
- Car Rental Agency

2. Registration pages:
- `register_customer.php`
- `register_agency.php`

3. Login page:
- Shared login page: `login.php`

4. Add new cars page (agency only):
- `agency_cars.php`
- Add car fields: vehicle model, vehicle number, seating capacity, rent per day
- Edit existing cars by agency owner

5. Available cars to rent page (public):
- `available_cars.php`
- Shows vehicle model, number, seating capacity, rent/day
- If customer logged in: shows rental days dropdown + start date + Rent Car action
- If not logged in and Rent Car clicked: redirects to login page
- If logged in as agency and Rent Car clicked: booking blocked

6. Agency view booked cars page:
- `agency_bookings.php`
- Shows all customer bookings for cars belonging to the logged-in agency

## Project Structure

- `config/database.php`: PDO connection setup
- `includes/auth.php`: session helpers, guards, flash messages
- `includes/layout.php`: shared header/footer/navbar
- UI uses Bootstrap classes only 
- `database/car_rental_agency.sql`: full schema + sample seed data

## Setup Instructions

1. Place project in web root:
- Example path: `/var/www/html/car-rental-agency`

2. Create database and import SQL:
```bash
mysql -u root -p < database/car_rental_agency.sql
```

3. Update DB credentials if needed:
- Edit `config/database.php`

4. Run using Apache/Nginx + PHP (or PHP built-in server):
```bash
php -S localhost:8000 -t .
```

5. Open:
- `http://localhost:8000/index.php`

## Seed Accounts (after SQL import)

All seed users use password: `password123`

- Agency 1: `agency1@example.com`
- Agency 2: `agency2@example.com`
- Customer: `customer1@example.com`

## Submission Packaging

From parent directory:
```bash
zip -r car-rental-agency.zip car-rental-agency
```

Upload `car-rental-agency.zip` to Google Drive and share the drive link.
