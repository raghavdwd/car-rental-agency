<?php

/*
    * This file contains the layout rendering functions for the Car Rental Agency application.
    * It provides a consistent header and footer across all pages, including navigation and flash message display.
    * In a real application, we might use a templating engine or a more sophisticated layout system.
*/

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function renderHeader(string $title): void
{
    $user = currentUser();
    $role = userRole();
    $flash = getFlash();
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo htmlspecialchars($title); ?> | RoadReady Rentals</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="index.php">RoadReady</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#rrNav" aria-controls="rrNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="rrNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="available_cars.php">Available Cars</a></li>
                    <?php if ($role === 'agency'): ?>
                        <li class="nav-item"><a class="nav-link" href="agency_cars.php">Add / Edit Cars</a></li>
                        <li class="nav-item"><a class="nav-link" href="agency_bookings.php">View Booked Cars</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if ($user): ?>
                        <li class="nav-item"><span class="nav-link">Hi, <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($role); ?>)</span></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="register_customer.php">Customer Register</a></li>
                        <li class="nav-item"><a class="nav-link" href="register_agency.php">Agency Register</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-md-5">
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>" role="alert">
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>
    <?php
}

function renderFooter(): void
{
    ?>
    </main>

    <footer class="py-4 mt-4 border-top bg-white">
        <div class="container text-center small text-muted">
            Car Rental Agency &copy; <?php echo date('Y'); ?>. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
    </html>
    <?php
}
