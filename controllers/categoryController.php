<?php 
require_once '../config/Database.php';
require_once '../models/categoryModel.php';

class categoryController {
    private $conn;
    private $catModel;

    public function __construct () {
        $db = new Database();
        $this->conn = $db->getConnection();
        $this->catModel = new categoryModel($this->conn);
    }

    public function displayCategory() {
    $categories = $this->catModel->getAllCategory();
    require_once __DIR__ . '/../views/index.php';
    }

    public function getSubcategories() {
        $input = json_decode(file_get_contents("php://input"), true);
        $category_id = $input['category_id'] ?? null;

        if ($category_id) {
            $subcategories = $this->catModel->getByCategoryId($category_id);
            echo json_encode(['success' => true, 'data' => $subcategories]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing category ID']);
        }
    }
}
?>