<?php

namespace App\Models;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use RuntimeException;

class CategoryModel extends BaseModel {
    private static $categoryType = null;

    public function getGraphQLType() {
        if (self::$categoryType === null) {
            self::$categoryType = new ObjectType([
            'name' => 'Category',
            'fields' => [
                'name' => ['type' => Type::string()],
                'products' => [
                    'type' => Type::listOf((new ProductModel())->getGraphQLType()),
                    'resolve' => fn($category) => $this->resolveProducts($category)
                ]
            ]
        ]);
    }
    return self::$categoryType;
    }
    
    public function resolve($root, $args ) {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE name = ?");
        $stmt->bind_param("s", $args['name']);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        $stmt->close();
        
        if (!$category) {
            throw new RuntimeException("Category not found");
        }
        
        return $category;
    }
    
    public function resolveAll() {
        $stmt = $this->conn->prepare("SELECT * FROM categories");
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        $stmt->close();
        return $categories;
    }
    private function resolveProducts($category) {
        $stmt = $this->conn->prepare(
            $category['name'] === 'all' 
                ? "SELECT * FROM products"
                : "SELECT * FROM products WHERE category = ?"
        );
        
        if ($category['name'] !== 'all') {
            $stmt->bind_param("s", $category['name']);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
        return $products;
    }
}