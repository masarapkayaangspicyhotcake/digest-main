<?php

include '../classes/database.class.php';

$db = new Database();
$conn = $db->connect();

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['delete'])) {
    // Delete related data from posts, likes, and comments
    $delete_image = $conn->prepare("SELECT image FROM `posts` WHERE created_by = ?");
    $delete_image->execute([$admin_id]);
    while ($fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($fetch_delete_image['image'])) {
            unlink('../uploaded_img/' . $fetch_delete_image['image']);
        }
    }

    $conn->prepare("DELETE FROM `posts` WHERE created_by = ?")->execute([$admin_id]);
    $conn->prepare("DELETE FROM `likes` WHERE account_id = ?")->execute([$admin_id]);
    $conn->prepare("DELETE FROM `comments` WHERE commented_by = ?")->execute([$admin_id]);

    // Delete the admin account from accounts table
    $delete_admin = $conn->prepare("DELETE FROM `accounts` WHERE account_id = ? AND role = 'admin'");
    $delete_admin->execute([$admin_id]);
    
    header('location:../components/admin_logout.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admins Accounts</title>

   <!-- Font Awesome CDN Link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS File Link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="accounts">

   <h1 class="heading">Admins Account</h1>

   <div class="box-container">
      <div class="box" style="order: -2;">
         <p>Register new admin</p>
         <a href="register_admin.php" class="option-btn" style="margin-bottom: .5rem;">Register</a>
      </div>

      <?php
      $select_account = $conn->prepare("SELECT * FROM `accounts` WHERE role = 'admin'");
      $select_account->execute();

      if ($select_account->rowCount() > 0) {
          while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {

              // Count the number of posts
              $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE created_by = ?");
              $count_admin_posts->execute([$fetch_accounts['account_id']]);
              $total_admin_posts = $count_admin_posts->rowCount();
      ?>

      <div class="box" style="order: <?= ($fetch_accounts['account_id'] == $admin_id) ? '-1' : '0'; ?>;">
         <p>Admin ID: <span><?= htmlspecialchars($fetch_accounts['account_id']); ?></span></p>
         <p>Username: <span><?= htmlspecialchars($fetch_accounts['firstname'] . ' ' . $fetch_accounts['lastname']); ?></span></p>
         <p>Total Posts: <span><?= $total_admin_posts; ?></span></p>
         <div class="flex-btn">
            <?php if ($fetch_accounts['account_id'] == $admin_id) { ?>
               <a href="update_profile.php" class="option-btn" style="margin-bottom: .5rem;">Update</a>
               <form action="" method="POST">
                  <input type="hidden" name="account_id" value="<?= $fetch_accounts['account_id']; ?>">
                  <button type="submit" name="delete" onclick="return confirm('Delete the account?');" class="delete-btn" style="margin-bottom: .5rem;">Delete</button>
               </form>
            <?php } ?>
         </div>
      </div>

      <?php
          }
      } else {
          echo '<p class="empty">No accounts available</p>';
      }
      ?>

   </div>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>
