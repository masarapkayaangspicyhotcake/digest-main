<?php
require_once '../classes/organization.class.php';
$organization = new Organization();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'add':
            echo $organization->addOrganization($_POST);
            break;
        case 'edit':
            echo $organization->editOrganization($_POST);
            break;
        case 'delete':
            echo $organization->deleteOrganization($_POST['org_id']);
            break;
    }
    exit();
}

// Load data for viewing
$organizations = $organization->getOrganizations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Organizational Chart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../js/organization.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Organizational Chart Management</h1>
        <button class="btn btn-primary" onclick="openModal('add')">Add Member</button>

        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Category</th>
                    <th>Date Appointed</th>
                    <th>Date Ended</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($organizations as $org) : ?>
                    <tr>
                        <td>
                            <?php if($org['image'] != ''){ ?>
                                <img src="uploads/members/<?= basename($org['image']); ?>" 
                                     alt="Member Image" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                            <?php } else { ?>
                                <img src="imgs/member.jpg" 
                                     alt="Default Image" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                            <?php } ?>
                        </td>
                        <td><?= htmlspecialchars($org['name']) ?></td>
                        <td><?= htmlspecialchars($org['position']) ?></td>
                        <td><?= htmlspecialchars($org['category']) ?></td>
                        <td><?= htmlspecialchars($org['date_appointed']) ?></td>
                        <td><?= htmlspecialchars($org['date_ended'] ?: 'Present') ?></td>
                        <td>
                            <button class="btn btn-warning" onclick="openModal('edit', <?= $org['org_id'] ?>)">Edit</button>
                            <button class="btn btn-danger" onclick="deleteOrganization(<?= $org['org_id'] ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
include 'modal.php';
        <!-- Modal for Add/Edit -->
        <div id="modal" class="modal" style="display: none;">
            <div class="modal-content" id="modal-content"></div>
        </div>
    </div>
</body>
</html>
