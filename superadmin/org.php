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
$groupedOrgs = [];

foreach ($organizations as $org) {
    $groupedOrgs[$org['category']][] = $org;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Organizational Chart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../js/organization.js"></script>
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h1 {
            color: #333;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #fff;
            border-radius: 5px;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
        }
        .btn-danger {
            background-color: #dc3545;
            color: #fff;
            border-radius: 5px;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 60%;
        }
    </style>
</head>
<body>
<?php include '../components/superadmin_sidebar.php' ?>

    <div class="container">
        <h1>Organizational Chart Management</h1>
        <button onclick="openModal('add')">Add Member</button>

        <?php foreach ($groupedOrgs as $category => $orgs) : ?>
            <h2><?= htmlspecialchars($category) ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Date Appointed</th>
                        <th>Date Ended</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orgs as $org) : ?>
                        <tr>
                            <td>
                                <?php if ($org['image'] != '') { ?>
                                    <img src="../uploads/members/<?= basename($org['image']); ?>" alt="Member Image">
                                <?php } else { ?>
                                    <img src="../imgs/member.jpg" alt="Default Image">
                                <?php } ?>
                            </td>
                            <td><?= htmlspecialchars($org['name']) ?></td>
                            <td><?= htmlspecialchars($org['position']) ?></td>
                            <td><?= htmlspecialchars($org['date_appointed']) ?></td>
                            <td><?= htmlspecialchars($org['date_ended'] ?: 'Present') ?></td>
                            <td>
                                <button class="btn-warning" onclick="openModal('edit', <?= $org['org_id'] ?>)">Edit</button>
                                <button class="btn-danger" onclick="deleteOrganization(<?= $org['org_id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>


        <div id="modal" class="modal">
            <div class="modal-content" id="modal-content"></div>
        </div>
    </div>
</body>
</html>