<?php 
class categoryModel {
    private $conn;

    public function __construct ($db) {
        $this->conn = $db;
    }

    public function getAllCategory () {
        $query = "SELECT * FROM category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getByCategoryId($category_id) {
        $stmt = $this->conn->prepare("SELECT * FROM sub_category WHERE category_id = ?");
        $stmt->bind_param('i', $category_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>