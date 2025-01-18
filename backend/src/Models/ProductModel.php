<?php

namespace App\Models;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use RuntimeException;

class ProductModel extends BaseModel {
    private static $productType = null;
    private static $galleryType = null;
    private static $priceType = null;
    public function getGraphQLType() {
        if (self::$productType === null) {
            self::$productType = new ObjectType([
            'name' => 'Product',
            'fields' => [
                'id' => ['type' => Type::string()],
                'name' => ['type' => Type::string()],
                'inStock' => ['type' => Type::boolean()],
                'stock' => ['type' => Type::int()],
                'description' => ['type' => Type::string()],
                'category' => ['type' => Type::string()],
                'brand' => ['type' => Type::string()],
                'category_id' => ['type' => Type::int()],
                'gallery' => [
                    'type' => Type::listOf($this->getGalleryType()),
                    'resolve' => fn($product) => $this->resolveGallery($product)
                ],
                'attributes' => [
                    'type' => Type::listOf((new ProductAttributeModel())->getGraphQLType()),
                    'resolve' => fn($product) => $this->resolveAttributes($product)
                ],
                'price' => [
                    'type' => Type::listOf($this->getPriceType()),
                    'resolve' => fn($product) => $this->resolvePrices($product)
                ]
            ]
        ]);
    }
    return self::$productType;
}
    private function getGalleryType() {
        if (self::$galleryType === null) {
            self::$galleryType = new ObjectType([
            'name' => 'ProductGallery',
            'fields' => [
                'product_id' => ['type' => Type::string()],
                'image_url' => ['type' => Type::string()]
            ]
        ]);
    }
    return self::$galleryType;
}
    
    private function getPriceType() {
        if (self::$priceType === null) {
            self::$priceType = new ObjectType([
            'name' => 'ProductPrice',
            'fields' => [
                'product_id' => ['type' => Type::string()],
                'amount' => ['type' => Type::float()],
                'currency_label' => ['type' => Type::string()],
                'currency_symbol' => ['type' => Type::string()]
            ]
        ]);
    }
    return self::$priceType;
}
    public function resolve($root, $args) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("s", $args['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        
        if (!$product) {
            throw new RuntimeException("Product not found");
        }
        
        return $product;
    }
    
    private function resolveGallery($product) {
        $stmt = $this->conn->prepare("SELECT * FROM product_gallery WHERE product_id = ?");
        $stmt->bind_param("s", $product['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $gallery = [];
        while ($row = $result->fetch_assoc()) {
            $gallery[] = $row;
        }
        $stmt->close();
        return $gallery;
    }
    
    private function resolveAttributes($product) {
        $attributeModel = new ProductAttributeModel();
        return $attributeModel->resolveForProduct($product['id']);
    }
    
    private function resolvePrices($product) {
        $stmt = $this->conn->prepare("SELECT * FROM product_prices WHERE product_id = ?");
        $stmt->bind_param("s", $product['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $prices = [];
        while ($row = $result->fetch_assoc()) {
            $prices[] = $row;
        }
        $stmt->close();
        return $prices;
    }
}