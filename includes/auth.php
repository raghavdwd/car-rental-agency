<?php

/*
    * This file contains authentication-related functions and session management for the Car Rental Agency application.
    * It provides utilities to check login status, manage user roles, and handle flash messages for user feedback.
    * In a real application, we would typically separate concerns further and use a more robust authentication system.
*/

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function userRole(): ?string
{
    return $_SESSION['user']['role'] ?? null;
}

function requireLogin(string $redirect = 'login.php'): void
{
    if (!isLoggedIn()) {
        header("Location: {$redirect}");
        exit;
    }
}


// Ensures the current user has the specified role. If not logged in or role doesn't match, redirects to the specified page.

function requireRole(string $role, string $redirect = 'index.php'): void
{
    requireLogin();

    if (userRole() !== $role) {
        header("Location: {$redirect}");
        exit;
    }
}


// Flash message functions for user feedback
function setFlash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type,
    ];
}


// Retrieves and clears the flash message from the session. Returns null if no flash message is set.
function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}
