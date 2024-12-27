<?php
namespace App\Models;

abstract class Category {
    protected int $id;
    protected string $name;

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    abstract public function getType(): string;

    public static function fetchAllCategories($conn): array {
        $conn = Database::getDatabaseConnection();
        $result = $conn->query("SELECT * FROM categories");
        $categories = [];

        while ($row = $result->fetch_assoc()) {
            switch ($row['name']) {
                case 'tech':
                    $categories[] = new TechCategory($row['id'], $row['name']);
                    break;
                case 'clothes':
                    $categories[] = new ClothesCategory($row['id'], $row['name']);
                    break;
                default:
                    $categories[] = new AllCategory($row['id'], $row['name']);
            }
        }

        return $categories;
    }
}

class TechCategory extends Category {
    public function getType(): string {
        return 'tech';
    }
}

class ClothesCategory extends Category {
    public function getType(): string {
        return 'clothes';
    }
}

class AllCategory extends Category {
    public function getType(): string {
        return 'all';
    }
}
?>