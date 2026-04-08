<?php
require 'vendor/autoload.php';

session_start();

echo "<pre>";
echo "Session data:\n";
print_r($_SESSION);
echo "\n\n";

// Check logged in status
echo "Logged in: " . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'Yes' : 'No') . "\n";
echo "User role: " . (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'Not set') . "\n";
echo "Department ID: " . (isset($_SESSION['department_id']) ? $_SESSION['department_id'] : 'Not set') . "\n";
echo "</pre>";
?>