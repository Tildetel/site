<?php
require_once __DIR__ . '/vendor/autoload.php'; // Load Composer packages

// Load environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['DB_SERVERNAME'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create the phonebook table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS phonebook (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tilde_name VARCHAR(255) NOT NULL,
    extension VARCHAR(20) NOT NULL,
    username VARCHAR(255) NOT NULL
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}
?>