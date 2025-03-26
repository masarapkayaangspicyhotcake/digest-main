<?php
require_once 'classes/organization.class.php';
$organization = new Organization();
$organizations = $organization->getOrganizations() ?: [];

// Handle AJAX requests for adding/editing/deleting members
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    switch ($action) {
        case 'add':
            echo $organization->addOrganization($_POST);
            exit();
        case 'edit':
            echo $organization->editOrganization($_POST);
            exit();
        case 'delete':
            echo $organization->deleteOrganization($_POST['org_id']);
            exit();
    }
}
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
    <style>
        .tree ul {
            padding-top: 20px; position: relative;
            transition: .5s;
        }
        .tree li {
            display: inline-block; text-align: center;
            list-style-type: none;
            position: relative; padding: 20px;
        }
        .tree li::before, .tree li::after {
            content: ''; position: absolute; top: 0; right: 50%;
            border-top: 2px solid #ccc; width: 50%; height: 20px;
        }
        .tree li::after {
            right: auto; left: 50%; border-left: 2px solid #ccc;
        }
        .tree li:only-child::after, .tree li:only-child::before {
            display: none;
        }
        .tree li:only-child { padding-top: 0; }
        .card {
            padding: 10px; background-color: #fff; border-radius: 8px;
            border: 1px solid #ccc; box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        img {
            width: 80px; height: 80px; border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Organizational Chart</h1>

        <div class="tree">
            <ul>
                <?php 
                if (!empty($organizations)) {
                    foreach ($organizations as $org) {
                        if ($org['position'] === 'Adviser') { ?>
                            <li>
                                <div class="card">
                                    <?php if($org['image'] != ''){ ?>
                                        <img src="uploads/members/<?= basename($org['image']); ?>" alt="Adviser Image">
                                    <?php } else { ?>
                                        <img src="./imgs/member.jpg" alt="Default Member Image">
                                    <?php } ?>
                                    <h4><?= htmlspecialchars($org['name']) ?></h4>
                                    <p><?= htmlspecialchars($org['position']) ?></p>
                                </div>
                                <ul>
                                    <?php foreach ($organizations as $childOrg) {
                                        if ($childOrg['position'] !== 'Adviser') { ?>
                                            <li>
                                                <div class="card">
                                                    <?php if($childOrg['image'] != ''){ ?>
                                                        <img src="uploads/members/<?= basename($childOrg['image']); ?>" alt="Member Image">
                                                    <?php } else { ?>
                                                        <img src="./imgs/member.jpg" alt="Default Member Image">
                                                    <?php } ?>
                                                    <h4><?= htmlspecialchars($childOrg['name']) ?></h4>
                                                    <p><?= htmlspecialchars($childOrg['position']) ?></p>
                                                </div>
                                            </li>
                                        <?php }
                                    } ?>
                                </ul>
                            </li>
                        <?php }
                    }
                } else {
                    echo "<p>No organizational members found.</p>";
                }
                ?>
            </ul>
        </div>
    </div>
</body>
</html>
