<?php

include '../classes/database.class.php';

$db = new Database();
$conn = $db->connect();

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
   exit();
}

if(isset($_POST['save'])){

   $post_id = $_GET['id'];
   $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
   $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
   $category = filter_var($_POST['category'] ?? '', FILTER_SANITIZE_STRING);
   $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

   // Check if category exists in the table
   $check_category = $conn->prepare("SHOW COLUMNS FROM `posts` LIKE 'category'");
   $check_category->execute();

   if ($check_category->rowCount() > 0) {
      $update_post = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, category = ?, status = ? WHERE post_id = ?");
      $update_post->execute([$title, $content, $category, $status, $post_id]);
   } else {
      $update_post = $conn->prepare("UPDATE `posts` SET title = ?, content = ?, status = ? WHERE post_id = ?");
      $update_post->execute([$title, $content, $status, $post_id]);
   }

   $message[] = 'Post updated!';

   // Image Upload Handling
   $old_image = $_POST['old_image'] ?? '';
   $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_img/'.$image;

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'Image size is too large!';
      } else {
         $update_image = $conn->prepare("UPDATE `posts` SET image = ? WHERE post_id = ?");
         move_uploaded_file($image_tmp_name, $image_folder);
         $update_image->execute([$image, $post_id]);
         if($old_image && file_exists('../uploaded_img/'.$old_image)){
            unlink('../uploaded_img/'.$old_image);
         }
         $message[] = 'Image updated!';
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Edit Post</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/superadmin_sidebar.php'; ?>

<section class="post-editor">

   <h1 class="heading">Edit Post</h1>

   <?php
      $post_id = $_GET['id'];
      $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE post_id = ?");
      $select_posts->execute([$post_id]);
      if($select_posts->rowCount() > 0){
         while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= htmlspecialchars($fetch_posts['image'] ?? ''); ?>">
      <input type="hidden" name="post_id" value="<?= htmlspecialchars($fetch_posts['post_id']); ?>">
      
      <p>Post Status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="<?= htmlspecialchars($fetch_posts['status']); ?>" selected><?= htmlspecialchars($fetch_posts['status']); ?></option>
         <option value="active">Active</option>
         <option value="deactive">Deactive</option>
      </select>

      <p>Post Title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required class="box" value="<?= htmlspecialchars($fetch_posts['title']); ?>">

      <p>Post Content <span>*</span></p>
      <textarea name="content" class="box" required maxlength="10000"><?= htmlspecialchars($fetch_posts['content']); ?></textarea>

      <p>Post Category <span>*</span></p>
      <select name="category" class="box" required>
         <option value="<?= htmlspecialchars($fetch_posts['category'] ?? ''); ?>" selected><?= htmlspecialchars($fetch_posts['category'] ?? 'Select'); ?></option>
         <option value="technology">Technology</option>
         <option value="fashion">Fashion</option>
         <option value="sports">Sports</option>
         <option value="music">Music</option>
         <option value="news">News</option>
      </select>

      <p>Post Image</p>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
      <?php if($fetch_posts['image']){ ?>
         <img src="../uploaded_img/<?= htmlspecialchars($fetch_posts['image']); ?>" class="image" alt="Post Image">
      <?php } ?>

      <div class="flex-btn">
         <input type="submit" value="Save Post" name="save" class="btn">
         <a href="view_posts.php" class="option-btn">Go Back</a>
      </div>
   </form>

   <?php
         }
      } else {
         echo '<p class="empty">No posts found!</p>';
      }
   ?>

</section>

</body>
</html>
