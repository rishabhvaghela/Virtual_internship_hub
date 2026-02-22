<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If not logged in
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /virtual_internship_hub/login.html");
        exit;
    }
}

// Role based access
function requireRole($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: /virtual_internship_hub/login.html");
        exit;
    }
}