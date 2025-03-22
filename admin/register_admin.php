<?php

require_once '../components/connect.php';

$db = new Database();
$conn = $db->connect();

session_start();

// Check if user is superadmin
if (!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'superadmin') {
    header('location: admin_login.php');
    exit();
}

if (isset($_POST['register'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'];
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password for security
    $role = $_POST['role'];

    // Ensure only superadmin and subadmin can be created
    if ($role !== 'superadmin' && $role !== 'subadmin') {
        echo "<script>alert('Invalid role selection.');</script>";
    } else {
        // Check for duplicate username or email
        $check_account = $conn->prepare("SELECT * FROM `accounts` WHERE user_name = ? OR email = ?");
        $check_account->execute([$user_name, $email]);

        if ($check_account->rowCount() > 0) {
            echo "<script>alert('Username or Email already exists.');</script>";
        } else {
            // Insert new admin
            $insert_admin = $conn->prepare("INSERT INTO `accounts` (firstname, lastname, middlename, user_name, email, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert_admin->execute([$firstname, $lastname, $middlename, $user_name, $email, $password, $role]);
            echo "<script>alert('Admin registered successfully.'); window.location.href='admin_accounts.php';</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Admin</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
<?php include '../components/superadmin_sidebar.php'; ?>

<section class="form-container">
    <form action="" method="post">
        <h1>Register Admin</h1>
        <input type="text" name="firstname" placeholder="First Name" required />
        <input type="text" name="lastname" placeholder="Last Name" required />
        <input type="text" name="middlename" placeholder="Middle Name" required />
        <input type="text" name="user_name" placeholder="Username" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        
        <!-- Role Selection (Superadmin can only create Subadmin or another Superadmin) -->
        <select name="role" required>
            <option value="subadmin">Subadmin</option>
            <option value="superadmin">Superadmin</option>
        </select>

        <button type="submit" name="register" class="btn">Register Admin</button>
    </form>
</section>

</body>
</html>
