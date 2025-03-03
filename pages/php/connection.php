<?php
    // Enable error reporting for development (Disable for production)
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Database configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "employee_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Set charset to utf8 to avoid encoding issues
    $conn->set_charset("utf8");

    // Check connection
    if ($conn->connect_error) {
        // Log the error for debugging
        error_log("Connection failed: " . $conn->connect_error);
        die("Connection failed: Please try again later.");
    }
?>
