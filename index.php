<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

renderHeader('Home');
?>

<section class="p-4 bg-white border rounded-2">
    <p class="badge bg-secondary mb-2">Car Rental Management</p>
    <h1 class="h4">Drive smarter with RoadReady Rentals</h1>
    <p class="mb-4">A simple, real-world rental workflow with separate customer and agency portals, transparent pricing, and secure booking.</p>
    <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-primary" href="available_cars.php">Browse Available Cars</a>
        <a class="btn btn-outline-primary" href="login.php">Login</a>
    </div>
</section>

<section class="row g-3 mt-1">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5>For Customers</h5>
                <p class="mb-3">Register, select rental days and start date, and book cars in a few clicks.</p>
                <a class="btn btn-sm btn-outline-primary" href="register_customer.php">Customer Registration</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5>For Agencies</h5>
                <p class="mb-3">Register your agency and manage your fleet inventory and bookings.</p>
                <a class="btn btn-sm btn-outline-primary" href="register_agency.php">Agency Registration</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5>Public Car Listing</h5>
                <p class="mb-3">View available cars without login. Booking requires customer authentication.</p>
                <a class="btn btn-sm btn-outline-primary" href="available_cars.php">View Cars</a>
            </div>
        </div>
    </div>
</section>

<?php renderFooter();
