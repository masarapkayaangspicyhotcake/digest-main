<?php
require_once '../components/connect.php';

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

    <div class="profile">
        <img src="../uploaded_img/<?= htmlspecialchars($fetch_profile['image'] ?? 'default.png'); ?>" alt="Profile Picture">
        <p><?= htmlspecialchars($fetch_profile['firstname'] . ' ' . $fetch_profile['lastname']); ?></p>
        <a href="../superadmin/update_profile.php" class="btn">Update Profile</a>
    </div>

    <nav class="navbar">
        <a href="../superadmin/superadmin_home.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
        <a href="../superadmin/add_posts.php"><i class="fas fa-pen"></i> <span>Add Posts</span></a>
        <a href="../superadmin/add_tejido.php"><i class="fas fa-pen"></i> <span>Add Tejido</span></a>
        <a href="../superadmin/view_posts.php"><i class="fas fa-home"></i> <span>Drafts</span></a>
        <a href="../superadmin/admin_accounts.php"><i class="fas fa-pen"></i> <span>Manage Admins</span></a>
        <a href="../superadmin/user_accounts_management.php"><i class="fas fa-pen"></i> <span>Users</span></a>
        <a href="../superadmin/view_posts.php"><i class="fas fa-eye"></i> <span>View Posts</span></a>
        <a href="../components/admin_logout.php" style="color:var(--red);" onclick="return confirm('Logout from the website?');">
            <i class="fas fa-right-from-bracket"></i><span>Logout</span>
        </a>
    </nav>
</header>

<div id="menu-btn" class="fas fa-bars"></div>
