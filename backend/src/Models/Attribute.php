<?php
namespace App\Models;

abstract class Attribute {
    protected string $id;
    protected string $productId;
    protected string $name;
    protected string $type;
    protected array $items = []; // Holds attribute items

    public function __construct(string $id, string $productId, string $name, string $type) {
        $this->id = $id;
        $this->productId = $productId;
        $this->name = $name;
        $this->type = $type;
    }

    abstract public function render(): array;

    public function getItems(): array {
        return $this->items;
    }

    public function loadItems($conn): void {
        $conn = Database::getDatabaseConnection();
        $stmt = $conn->prepare("SELECT * FROM product_attribute_items WHERE product_id = ? AND attribute_name = ?");
        $stmt->bind_param("ss", $this->productId, $this->name);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $this->items[] = [
                'item_id' => $row['item_id'],
                'display_value' => $row['display_value'],
                'value' => $row['value']
            ];
        }
    }
}

class TextAttribute extends Attribute {
    public function render(): array {
        return [
            'type' => 'text',
            'name' => $this->name,
            'items' => $this->getItems()
        ];
    }
}

class ColorAttribute extends Attribute {
    public function render(): array {
        return [
            'type' => 'swatch',
            'name' => $this->name,
            'items' => $this->getItems()
        ];
    }
}
