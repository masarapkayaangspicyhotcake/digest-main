<?php
require_once '../classes/database.class.php';

$db = new Database();
$conn = $db->connect();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('location:../admin/admin_login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];
$role = $_SESSION['role'] ?? '';

// Fetch profile data safely
$select_profile = $conn->prepare("SELECT firstname, lastname, image FROM `accounts` WHERE account_id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (!$fetch_profile) {
    echo '<p class="error">Profile not found. Please contact support.</p>';
    exit();
}
?>

<header class="header">

   <a href="dashboard.php" class="logo">Sub-Admin<span>Panel</span></a>

   <div class="profile">
      <?php
         // Corrected query to use 'account_id' instead of 'id'
         $select_profile = $conn->prepare("SELECT * FROM `accounts` WHERE account_id = ?");
         $select_profile->execute([$admin_id]);
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      ?>
      <p><?= $fetch_profile['firstname'] . ' ' . $fetch_profile['lastname']; ?></p>
      <a href="../admin/update_profile.php" class="btn">update profile</a>
   </div>

   <nav class="navbar">
      <a href="../admin/dashboard.php"><i class="fas fa-home"></i> <span>home</span></a>
      <a href="../admin_content/add_posts.php"><i class="fas fa-pen"></i> <span>add posts</span></a>
      <a href="../admin_content/view_posts.php"><i class="fas fa-eye"></i> <span>view posts</span></a>
      <a href="../admin_content/admin_accounts.php"><i class="fas fa-user"></i> <span>accounts</span></a>
      <a href="../components/admin_logout.php" style="color:var(--red);" onclick="return confirm('logout from the website?');"><i class="fas fa-right-from-bracket"></i><span>logout</span></a>
   </nav>


</header>

<div id="menu-btn" class="fas fa-bars"></div>