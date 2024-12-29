<?php

namespace App\Controller;

require __DIR__ . '/../../vendor/autoload.php';

use App\Models\Database;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductAttribute;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

// Abstract base model class
abstract class BaseModel {
    protected $conn;
    
    public function __construct() {
        $this->conn = Database::getDatabaseConnection();
    }
    
    abstract public function getGraphQLType();
    abstract public function resolve($root, $args = []);
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Product Model
class ProductModel extends BaseModel {
    public function getGraphQLType() {
        return new ObjectType([
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
    
    private function getGalleryType() {
        return new ObjectType([
            'name' => 'ProductGallery',
            'fields' => [
                'product_id' => ['type' => Type::string()],
                'image_url' => ['type' => Type::string()]
            ]
        ]);
    }
    
    private function getPriceType() {
        return new ObjectType([
            'name' => 'ProductPrice',
            'fields' => [
                'product_id' => ['type' => Type::string()],
                'amount' => ['type' => Type::float()],
                'currency_label' => ['type' => Type::string()],
                'currency_symbol' => ['type' => Type::string()]
            ]
        ]);
    }
    
    public function resolve($root, $args = []) {
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

// Category Model
class CategoryModel extends BaseModel {
    public function getGraphQLType() {
        return new ObjectType([
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
    
    public function resolve($root, $args = []) {
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

// Product Attribute Model
class ProductAttributeModel extends BaseModel {
    public function getGraphQLType() {
        return new ObjectType([
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
    
    private function getAttributeItemsType() {
        return new ObjectType([
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
    
    public function resolve($root, $args = []) {
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

// Order Model for mutations
class OrderModel extends BaseModel {
    public function getGraphQLType() {
        return new InputObjectType([
            'name' => 'OrderInput',
            'fields' => [
                'total' => Type::float(),
                'products' => Type::listOf(
                    new InputObjectType([
                        'name' => 'ProductInput',
                        'fields' => [
                            'id' => Type::string(),
                            'name' => Type::string(),
                            'price' => Type::float(),
                            'quantity' => Type::int()
                        ]
                    ])
                )
            ]
        ]);
    }
    
    public function resolve($root, $args = []) {
        $orderData = $args['orderData'];
        
        // Insert main order
        $stmt = $this->conn->prepare("INSERT INTO orders (total_price) VALUES (?)");
        $stmt->bind_param("d", $orderData['total']);
        $stmt->execute();
        $orderId = $stmt->insert_id;
        $stmt->close();
        
        // Insert order products
        foreach ($orderData['products'] as $product) {
            $stmt = $this->conn->prepare(
                "INSERT INTO order_products (order_id, product_id, product_name, price, quantity) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "issdi",
                $orderId,
                $product['id'],
                $product['name'],
                $product['price'],
                $product['quantity']
            );
            $stmt->execute();
            $stmt->close();
        }
        
        return true;
    }
}

class GraphQL {
    static public function handle() {
        try {
            // Initialize models
            $productModel = new ProductModel();
            $categoryModel = new CategoryModel();
            $orderModel = new OrderModel();
            
            // Define Query Type
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'product' => [
                        'type' => $productModel->getGraphQLType(),
                        'args' => ['id' => Type::nonNull(Type::string())],
                        'resolve' => fn($root, $args) => $productModel->resolve($root, $args)
                    ],
                    'category' => [
                        'type' => $categoryModel->getGraphQLType(),
                        'args' => ['name' => Type::nonNull(Type::string())],
                        'resolve' => fn($root, $args) => $categoryModel->resolve($root, $args)
                    ]
                ]
            ]);
            
            // Define Mutation Type
            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'placeOrder' => [
                        'type' => Type::boolean(),
                        'args' => ['orderData' => $orderModel->getGraphQLType()],
                        'resolve' => fn($root, $args) => $orderModel->resolve($root, $args)
                    ]
                ]
            ]);
            
            // Create Schema
            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );
            
            // Execute Query
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
            
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
            
            $result = GraphQLBase::executeQuery($schema, $query, [], null, $variableValues);
            $output = $result->toArray();
            
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($output);
    }
}

GraphQL::handle();
