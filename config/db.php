<?php
// ========================================================
// Digital Memory Jar - Database Configuration & Helpers
// ========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'digital_memory_jar';

try {
    // Connect to MySQL database using PDO
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // If the database does not exist yet, create it and the required tables
    try {
        $pdo = new PDO("mysql:host={$db_host};charset=utf8mb4", $db_user, $db_pass);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4");
        $pdo->exec("USE `{$db_name}`");

        // Users table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(150) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Memories table
        $pdo->exec("CREATE TABLE IF NOT EXISTS `memories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `title` VARCHAR(150) NOT NULL,
            `description` TEXT NOT NULL,
            `memory_date` DATE NOT NULL,
            `category` VARCHAR(50) NOT NULL,
            `mood` VARCHAR(30) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        )");
    } catch (PDOException $ex) {
        die("Database connection failed. Please make sure MySQL is started in your XAMPP Control Panel.");
    }
}

/**
 * Escape HTML special characters for safe output
 */
function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date for friendly display (e.g., October 26, 2026)
 */
function format_memory_date($date_str) {
    if (empty($date_str)) return '';
    return date('F j, Y', strtotime($date_str));
}

/**
 * Category icon and color mapping
 */
function get_category_meta($category) {
    $categories = [
        'Happy Moment'     => ['icon' => '🌸', 'color' => '#F4C7D9'],
        'Achievement'      => ['icon' => '🏆', 'color' => '#FFE8A3'],
        'Friendship'       => ['icon' => '👯‍♀️', 'color' => '#C8B6E2'],
        'Family'           => ['icon' => '🏡', 'color' => '#FAD4C0'],
        'Travel'           => ['icon' => '✈️', 'color' => '#C8D8C0'],
        'Positive Message' => ['icon' => '💌', 'color' => '#D8E2DC'],
        'Other'            => ['icon' => '✨', 'color' => '#E8E8E4']
    ];
    return $categories[$category] ?? ['icon' => '✨', 'color' => '#E8E8E4'];
}
