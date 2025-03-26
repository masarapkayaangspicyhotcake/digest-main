<?php

include '../classes/database.class.php';

$db = new Database();
$conn = $db->connect();

session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('location: admin_login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch user role
$select_role = $conn->prepare("SELECT role FROM `accounts` WHERE account_id = ?");
$select_role->execute([$admin_id]);
$user = $select_role->fetch(PDO::FETCH_ASSOC);

// Redirect based on role
if ($user) {
    if ($user['role'] === 'superadmin') {
        header('location: ../superadmin/superadmin_home.php');
        exit();
    } elseif ($user['role'] === 'subadmin') {
        header('location: ../subadmin/subadmin_home.php');
        exit();
    } else {
        echo "Unauthorized Access!";
        exit();
    }
} else {
    echo "User not found.";
}
?>
