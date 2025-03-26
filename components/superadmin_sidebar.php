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

        <div class="dropdown">
            <a href="#" class="dropdown-toggle" id="dropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-pen"></i> <span>Manage Posts</span>
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                <li><a href="../superadmin/add_posts.php" class="dropdown-item"><i class="fas fa-file-alt"></i> Add Posts</a></li>
                <li><a href="../superadmin/add_tejido.php" class="dropdown-item"><i class="fas fa-newspaper"></i> Add Tejido</a></li>
            </ul>
        </div>

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

<!-- Bootstrap JS for dropdowns -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
    }
</style>
