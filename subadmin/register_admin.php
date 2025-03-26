<?php

require_once '../classes/database.class.php';

$db = new Database();
$conn = $db->connect();

session_start();

// Check if user is superadmin


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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>
<?php include '../components/subadmin_sidebar.php'; ?>

<section class="form-container">
  <form action="" method="post" enctype="multipart/form-data">
    <h1>Register Admin</h1>

    <!-- First Name -->
    <label for="firstname">First Name</label>
    <input type="text" name="firstname" id="firstname" placeholder="Enter First Name" required />

    <!-- Last Name -->
    <label for="lastname">Last Name</label>
    <input type="text" name="lastname" id="lastname" placeholder="Enter Last Name" required />

    <!-- Middle Name -->
    <label for="middlename">Middle Name</label>
    <input type="text" name="middlename" id="middlename" placeholder="Enter Middle Name" required />

    <!-- Username -->
    <label for="user_name">Username</label>
    <input type="text" name="user_name" id="user_name" placeholder="Create a Username" required />

    <!-- Email -->
    <label for="email">Email</label>
    <input type="email" name="email" id="email" placeholder="Enter Email" required />

    <!-- Password -->
    <label for="password">Password</label>
    <input type="password" name="password" id="password" placeholder="Enter Password" required />

    <!-- Confirm Password -->
    <label for="confirm_password">Confirm Password</label>
    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required />

    <!-- Profile Image (Optional) -->
    <label for="image">Profile Image (Optional)</label>
    <input type="file" name="image" id="image" accept="image/*">

    <!-- Role Selection -->
    <label for="role">Select Role</label>
    <select name="role" id="role" required>
      <option value="subadmin">Subadmin</option>
      <option value="superadmin">Superadmin</option>
    </select>

    <button type="submit" name="register" class="btn">Register Admin</button>
  </form>
</section>

<!-- CSS Styling -->
<style>
  .form-container {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-top: 50px;
  }

  form {
    background-color: #fff;
    border-radius: 12px;
    padding: 40px;
    width: 600px; /* Larger Form */
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
  }

  h1 {
    font-size: 32px;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
  }

  label {
    font-size: 14px;
    margin-top: 12px;
    display: block;
    color: #555;
  }

  input, select, button {
    width: 100%;
    padding: 14px;
    margin-top: 8px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
  }

  input:focus, select:focus {
    border-color: #007bff;
    outline: none;
  }

  button {
    background-color: #007bff;
    color: white;
    border: none;
    font-size: 16px;
    cursor: pointer;
    margin-top: 20px;
  }

  button:hover {
    background-color: #0056b3;
  }
</style>


</body>
</html>
