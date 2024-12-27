<?php
namespace App\Models;

abstract class Product {
    protected string $id;
    protected string $name;
    protected string $description;
    protected float $price;
    protected int $stock;
    protected bool $inStock;
    protected int $categoryId;

    public function __construct(string $id, string $name, string $description, float $price, int $stock, bool $inStock, int $categoryId) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->stock = $stock;
        $this->inStock = $inStock;
        $this->categoryId = $categoryId;
    }

    abstract public function getDetails(): array;

    public static function fetchProductsByCategory($conn, int $categoryId): array {
        $conn = Database::getDatabaseConnection();
        $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = new PhysicalProduct(
                $row['id'],
                $row['name'],
                $row['description'],
                $row['price'],
                $row['stock'],
                $row['inStock'],
                $row['category_id']
            );
        }

        return $products;
    }
}

class PhysicalProduct extends Product {
    public function getDetails(): array {
        return [
            'type' => 'physical',
            'stock' => $this->stock,
            'inStock' => $this->inStock,
            'price' => $this->price
        ];
    }
}
?>