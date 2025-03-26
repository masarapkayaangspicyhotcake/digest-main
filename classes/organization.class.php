<?php
require_once 'database.class.php';

class Organization {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Add Organization Member
    public function addOrganization($data) {
        $imagePath = $this->uploadImage($_FILES['image']);
        $query = "INSERT INTO organizational_chart (name, image, position, category, date_appointed, date_ended) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            $data['name'],
            $imagePath,
            $data['position'],
            $data['category'],
            $data['date_appointed'],
            $data['date_ended'] ?? null
        ]);
        return 'Member Added Successfully!';
    }

    // Edit Organization Member
    public function editOrganization($data) {
        $imagePath = !empty($_FILES['image']['name']) ? $this->uploadImage($_FILES['image']) : $data['existing_image'];
        $query = "UPDATE organizational_chart SET name = ?, image = ?, position = ?, category = ?, date_appointed = ?, date_ended = ? WHERE org_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            $data['name'],
            $imagePath,
            $data['position'],
            $data['category'],
            $data['date_appointed'],
            $data['date_ended'] ?? null,
            $data['org_id']
        ]);
        return 'Member Updated Successfully!';
    }

    // Upload Image and Save Path
    private function uploadImage($image) {
        if ($image['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . '../../uploads/members/';

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . '_' . basename($image['name']);
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                return 'uploads/members/' . $fileName;
            }
        }
        return null;
    }

    // Soft Delete Organization Member
    public function deleteOrganization($org_id) {
        $query = "UPDATE organizational_chart SET is_deleted = 1 WHERE org_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$org_id]);
        return 'Member Deleted Successfully!';
    }

    // Get Organization Members
    public function getOrganizations() {
        $query = "SELECT * FROM organizational_chart WHERE is_deleted = 0 ORDER BY category, position, date_appointed DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>