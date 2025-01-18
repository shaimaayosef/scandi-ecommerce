<?php

namespace App\Models;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductAttributeModel extends BaseModel {
    private static $attributeType = null;
    private static $attributeItemsType = null;

    public function getGraphQLType() {
        if (self::$attributeType === null) {
            self::$attributeType = new ObjectType([
            'name' => 'ProductAttributes',
            'fields' => [
                'id' => ['type' => Type::string()],
                'attribute_name' => ['type' => Type::string()],
                'attribute_type' => ['type' => Type::string()],
                'product_id' => ['type' => Type::string()],
                'items' => [
                    'type' => Type::listOf($this->getAttributeItemsType()),
                    'resolve' => fn($attribute) => $this->resolveItems($attribute)
                ]
            ]
        ]);
    }
    return self::$attributeType;
}
    
    private function getAttributeItemsType() {
        if (self::$attributeItemsType === null) {
            self::$attributeItemsType = new ObjectType([
            'name' => 'ProductAttributeItems',
            'fields' => [
                'item_id' => ['type' => Type::string()],
                'attribute_name' => ['type' => Type::string()],
                'display_value' => ['type' => Type::string()],
                'value' => ['type' => Type::string()],
                'product_id' => ['type' => Type::string()]
            ]
        ]);
    }
    return self::$attributeItemsType;
}
    public function resolve($root, $args ) {
        return $this->resolveForProduct($args['productId']);
    }
    
    public function resolveForProduct($productId) {
        $stmt = $this->conn->prepare("SELECT * FROM product_attributes WHERE product_id = ?");
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $attributes = [];
        while ($row = $result->fetch_assoc()) {
            $attributes[] = $row;
        }
        $stmt->close();
        return $attributes;
    }
    
    private function resolveItems($attribute) {
        $stmt = $this->conn->prepare("SELECT * FROM product_attribute_items WHERE product_id = ? AND attribute_name = ?");
        $stmt->bind_param("ss", $attribute['product_id'], $attribute['attribute_name']);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }
}