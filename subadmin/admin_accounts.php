<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../components/connect.php';

$db = new Database();
$conn = $db->connect();


$admin_id = $_SESSION['admin_id'] ?? null;
$admin_role = $_SESSION['role'] ?? null;



// Handle account deletion
if (isset($_POST['delete'])) {
    $account_id = $_POST['account_id'];

    // Delete related data
    $delete_image = $conn->prepare("SELECT image FROM `posts` WHERE created_by = ?");
    $delete_image->execute([$account_id]);
    while ($fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($fetch_delete_image['image'])) {
            unlink('../uploaded_img/' . $fetch_delete_image['image']);
        }
    }

    $conn->prepare("DELETE FROM `posts` WHERE created_by = ?")->execute([$account_id]);
    $conn->prepare("DELETE FROM `likes` WHERE account_id = ?")->execute([$account_id]);
    $conn->prepare("DELETE FROM `comments` WHERE commented_by = ?")->execute([$account_id]);
    $conn->prepare("DELETE FROM `accounts` WHERE account_id = ? AND role IN ('subadmin', 'superadmin')")->execute([$account_id]);
    
    echo "<script>alert('Account deleted successfully'); window.location.href='admin_accounts.php';</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admins Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/subadmin_sidebar.php'; ?>

<section class="accounts">
    <h1 class="heading">Manage Admins</h1>

    <div class="box-container">
        <div class="box" style="order: -2;">
            <p>Register new admin</p>
            <a href="../superadmin/register_admin.php" class="option-btn" style="margin-bottom: .5rem;">Register</a>
        </div>

        <?php
        // Select all subadmins and superadmins
        $select_account = $conn->prepare("SELECT * FROM `accounts` WHERE role IN ('subadmin', 'superadmin')");
        $select_account->execute();

        if ($select_account->rowCount() > 0) {
            while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
                $count_admin_posts = $conn->prepare("SELECT * FROM `posts` WHERE created_by = ?");
                $count_admin_posts->execute([$fetch_accounts['account_id']]);
                $total_admin_posts = $count_admin_posts->rowCount();
        ?>

        <div class="box" style="order: <?= ($fetch_accounts['account_id'] == $admin_id) ? '-1' : '0'; ?>;">
            <p>Admin ID: <span><?= htmlspecialchars($fetch_accounts['account_id']); ?></span></p>
            <p>Username: <span><?= htmlspecialchars($fetch_accounts['firstname'] . ' ' . $fetch_accounts['lastname']); ?></span></p>
            <p>Role: <span><?= htmlspecialchars($fetch_accounts['role']); ?></span></p>
            <p>Total Posts: <span><?= $total_admin_posts; ?></span></p>
            <div class="flex-btn">
                <a href="update_profile.php?id=<?= $fetch_accounts['account_id']; ?>" class="option-btn">Update</a>
                <form action="" method="POST">
                    <input type="hidden" name="account_id" value="<?= $fetch_accounts['account_id']; ?>">
                    <button type="submit" name="delete" onclick="return confirm('Delete the account?');" class="delete-btn">Delete</button>
                </form>
            </div>
        </div>

        <?php
            }
        } else {
            echo '<p class="empty">No subadmins or superadmins available</p>';
        }
        ?>
    </div>
</section>

</body>
</html>
