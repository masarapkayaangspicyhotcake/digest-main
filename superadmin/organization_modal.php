<?php
require_once '../classes/organization.class.php';

$organization = new Organization();
$action = $_GET['action'];
$orgId = isset($_GET['orgId']) ? intval($_GET['orgId']) : null;
$data = ['name' => '', 'image' => '', 'position' => '', 'category' => '', 'date_appointed' => '', 'date_ended' => ''];

if ($action === 'edit' && $orgId) {
    $members = $organization->getOrganizations();
    foreach ($members as $member) {
        if ($member['org_id'] == $orgId) {
            $data = $member;
            break;
        }
    }
}

?>

<div class="modal-header">
    <h5><?= $action === 'add' ? 'Add Member' : 'Edit Member' ?></h5>
    <button onclick="closeModal()">&times;</button>
</div>
<div class="modal-body">
    <form id="orgForm" enctype="multipart/form-data">
        <input type="hidden" name="org_id" value="<?= htmlspecialchars($orgId) ?>">
        
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
        
        <label>Position:</label>
        <input type="text" name="position" value="<?= htmlspecialchars($data['position']) ?>" required>
        
        <label>Category:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($data['category']) ?>" required>
        
        

        <label>Date Appointed:</label>
        <input type="date" name="date_appointed" value="<?= htmlspecialchars($data['date_appointed']) ?>" required>
        
        <label>Date Ended (if applicable):</label>
        <input type="date" name="date_ended" value="<?= htmlspecialchars($data['date_ended']) ?>">

        <label>Image:</label>
        <input type="file" name="image" accept="image/*">

        <button type="button" onclick="submitForm('<?= $action ?>')">Submit</button>
    </form>
</div>