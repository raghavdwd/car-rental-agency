<?php

/*
    * This file handles user logout for the Car Rental Agency application.
    * It clears the user's session and redirects them to the login page with a flash message indicating successful logout.
*/
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

session_start();
setFlash('Logged out successfully.');

header('Location: login.php');
exit;
