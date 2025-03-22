<?php

require_once '../components/connect.php';

$db = new Database();
$conn = $db->connect();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('location: admin_login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch profile data
$select_profile = $conn->prepare("SELECT firstname, lastname FROM `accounts` WHERE account_id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (!$fetch_profile) {
    echo '<p class="error">Profile not found. Please contact support.</p>';
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

    <!-- Custom CSS File -->
    <link rel="stylesheet" href="../css/admin_style.css" />
</head>

<body>

<?php include '../components/superadmin_sidebar.php'; ?>

<section class="dashboard">

    <h1 class="heading">Dashboard</h1>

    <div class="box-container">

        <div class="box">
            <h3>Welcome!</h3>
            <p><?= htmlspecialchars($fetch_profile['firstname'] . ' ' . $fetch_profile['lastname']); ?></p>
            <a href="update_profile.php" class="btn">Update Profile</a>
        </div>

        <!-- Published Posts Section -->
        <div class="box">
            <?php
            $select_posts = $conn->prepare("SELECT COUNT(*) FROM `posts` WHERE created_by = ? AND status = 'published'");
            $select_posts->execute([$admin_id]);
            $numbers_of_posts = $select_posts->fetchColumn();
            ?>
            <h3><?= $numbers_of_posts; ?></h3>
            <p>Published Posts</p>
            <a href="add_posts.php" class="btn">Add New Post</a>
        </div>

        <!-- Tejido Section -->
        <div class="box">
            <?php
            $select_tejido = $conn->prepare("SELECT COUNT(*) FROM `tejido` WHERE created_by = ?");
            $select_tejido->execute([$admin_id]);
            $numbers_of_tejido = $select_tejido->fetchColumn();
            ?>
            <h3><?= $numbers_of_tejido; ?></h3>
            <p>Tejido Added</p>
            <a href="add_tejido.php" class="btn">Add Tejido</a>
        </div>


        <div class="box">
        <?php
         $select_admins = $conn->prepare("SELECT * FROM `accounts` WHERE role IN ('superadmin', 'subadmin')");
         $select_admins->execute();
         $numbers_of_admins = $select_admins->rowCount();
        ?>
        <h3><?= $numbers_of_admins; ?></h3>
        <p>Articles</p>
        <a href="add_articles.php" class="btn">Add Articles</a>
       </div>
       
        <!-- Draft Posts Section -->
        <div class="box">
            <?php
            $select_deactive_posts = $conn->prepare("SELECT COUNT(*) FROM `posts` WHERE created_by = ? AND status = 'draft'");
            $select_deactive_posts->execute([$admin_id]);
            $numbers_of_deactive_posts = $select_deactive_posts->fetchColumn();
            ?>
            <h3><?= $numbers_of_deactive_posts; ?></h3>
            <p>Draft Posts</p>
            <a href="view_posts.php" class="btn">See Drafts</a>
        </div>

        <!-- Users Account Section -->
        <div class="box">
            <?php
            $select_users = $conn->prepare("SELECT COUNT(*) FROM `accounts` WHERE role = 'user'");
            $select_users->execute();
            $numbers_of_users = $select_users->fetchColumn();
            ?>
            <h3><?= $numbers_of_users; ?></h3>
            <p>User Accounts</p>
            <a href="user_accounts_management.php" class="btn">See Users</a>
        </div>

        
        <!-- Admin Accounts Section -->
        <div class="box">
            <?php
            $select_admins = $conn->prepare("SELECT COUNT(*) FROM `accounts` WHERE role IN ('superadmin', 'subadmin')");
            $select_admins->execute();
            $numbers_of_admins = $select_admins->fetchColumn();
            ?>
            <h3><?= $numbers_of_admins; ?></h3>
            <p>Admin Accounts</p>
            <a href="admin_accounts.php" class="btn">Manage Admins</a>
        </div>

        <!-- Comments Section -->
        <div class="box">
            <?php
            $select_comments = $conn->prepare("SELECT COUNT(*) FROM `comments` WHERE commented_by = ?");
            $select_comments->execute([$admin_id]);
            $numbers_of_comments = $select_comments->fetchColumn();
            ?>
            <h3><?= $numbers_of_comments; ?></h3>
            <p>Comments Added</p>
            <a href="comments.php" class="btn">See Comments</a>
        </div>

        <!-- Likes Section -->
        <div class="box">
            <?php
            $select_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE account_id = ?");
            $select_likes->execute([$admin_id]);
            $numbers_of_likes = $select_likes->fetchColumn();
            ?>
            <h3><?= $numbers_of_likes; ?></h3>
            <p>Total Likes</p>
            <a href="total_likes.php" class="btn">See Total Likes</a>
        </div>

    </div>

</section>

<script src="../js/admin_script.js"></script>

</body>

</html>
