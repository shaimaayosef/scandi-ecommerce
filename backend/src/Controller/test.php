<?php
namespace App\Controller;
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require __DIR__ . '/../../vendor/autoload.php';

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

class GraphQL {
    private static function getDatabaseConnection() {
        $servername = "localhost";
        $username = "scandiAdmin";
        $password = "1234";
        $dbname = "scandi2";
        
        $conn = new \mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            throw new RuntimeException("Connection failed: " . $conn->connect_error);
        }
        
        return $conn;
    }

    private static function fetchProductGallery($productId) {
        $conn = self::getDatabaseConnection();
        $stmt = $conn->prepare("SELECT image_url FROM product_gallery WHERE product_id = ?");
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $gallery = [];
        while ($row = $result->fetch_assoc()) {
            $gallery[] = $row['image_url'];
        }
        $stmt->close();
        $conn->close();
        return $gallery;
    }
    
    private static function fetchProductAttributes($productId) {
        $conn = self::getDatabaseConnection();
        $stmt = $conn->prepare("SELECT attribute_name, attribute_type FROM product_attributes WHERE product_id = ?");
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $attributes = [];
        while ($row = $result->fetch_assoc()) {
            $row['items'] = self::fetchAttributeItems($row['attribute_name'], $productId);
            $attributes[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $attributes;
    }
    
    private static function fetchAttributeItems($attributeName, $productId) {
        $conn = self::getDatabaseConnection();
        $stmt = $conn->prepare("SELECT display_value, value, item_id FROM product_attribute_items WHERE attribute_name = ? AND product_id = ?");
        $stmt->bind_param("ss", $attributeName, $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $items;
    }
    
    private static function fetchProductPrices($productId) {
        $conn = self::getDatabaseConnection();
        $stmt = $conn->prepare("SELECT amount, currency_label, currency_symbol FROM product_prices WHERE product_id = ?");
        $stmt->bind_param("s", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $prices = [];
        while ($row = $result->fetch_assoc()) {
            $prices[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $prices;
    }

    static public function handle() {
        try {
            // Define Product Type with related fields
            $productType = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => ['type' => Type::string()],
                    'name' => ['type' => Type::string()],
                    'inStock' => ['type' => Type::boolean()],
                    'description' => ['type' => Type::string()],
                    'category' => ['type' => Type::string()],
                    'brand' => ['type' => Type::string()],
                    'gallery' => [
                        'type' => Type::listOf(Type::string()),
                        'resolve' => function($product) {
                            return self::fetchProductGallery($product['id']);
                        }
                    ],
                    'attributes' => [
                        'type' => Type::listOf(new ObjectType([
                            'name' => 'Attribute',
                            'fields' => [
                                'attribute_name' => ['type' => Type::string()],
                                'attribute_type' => ['type' => Type::string()],
                                'items' => [
                                    'type' => Type::listOf(new ObjectType([
                                        'name' => 'AttributeItem',
                                        'fields' => [
                                            'display_value' => ['type' => Type::string()],
                                            'value' => ['type' => Type::string()],
                                            'item_id' => ['type' => Type::string()]
                                        ]
                                    ])),
                                    'resolve' => function($attribute) {
                                        return self::fetchAttributeItems($attribute['attribute_name'], $attribute['product_id']);
                                    }
                                ]
                            ]
                        ])),
                        'resolve' => function($product) {
                            $attributes = self::fetchProductAttributes($product['id']);
                            foreach ($attributes as &$attribute) {
                                $attribute['items'] = self::fetchAttributeItems($attribute['attribute_name'], $product['id']);
                            }
                            return $attributes;
                        }
                    ],
                    'prices' => [
                        'type' => Type::listOf(new ObjectType([
                            'name' => 'Price',
                            'fields' => [
                                'amount' => ['type' => Type::float()],
                                'currency_label' => ['type' => Type::string()],
                                'currency_symbol' => ['type' => Type::string()]
                            ]
                        ])),
                        'resolve' => function($product) {
                            return self::fetchProductPrices($product['id']);
                        }
                    ]
                ]
            ]);

             // Define Category Type
             $categoryType = new ObjectType([
                'name' => 'Category',
                'fields' => [
                    'id' => ['type' => Type::int()],
                    'name' => ['type' => Type::string()],
                    'products' => [
                        'type' => Type::listOf($productType),
                        'resolve' => function($category) {
                            $conn = self::getDatabaseConnection();
                            $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
                            $stmt->bind_param("s", $category['name']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $products = [];
                            while ($row = $result->fetch_assoc()) {
                                // Fetch each product's gallery, attributes, and prices
                                $row['gallery'] = self::fetchProductGallery($row['id']);
                                $row['attributes'] = self::fetchProductAttributes($row['id']);
                                $row['prices'] = self::fetchProductPrices($row['id']);
                                $products[] = $row;
                            }
                            $stmt->close();
                            $conn->close();
                            return $products;
                        }
                    ]
                ]
            ]);

            // Define Query Type with multiple table queries
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf($categoryType),
                        'resolve' => function() {
                            $conn = self::getDatabaseConnection();
                            $result = $conn->query("SELECT * FROM categories");
                            $categories = [];
                            while ($row = $result->fetch_assoc()) {
                                $categories[] = $row;
                            }
                            $conn->close();
                            return $categories;
                        }
                    ],
                    'category' => [
                        'type' => Type::listOf($categoryType),
                        'args' => [
                            'name' => ['type' => Type::nonNull(Type::string())]
                        ],
                        'resolve' => function ($root, $args) {
                            $conn = self::getDatabaseConnection();
                            
                            // Get the category based on name
                            $stmt = $conn->prepare("SELECT * FROM categories WHERE name = ?");
                            $stmt->bind_param("s", $args['name']);
                            $stmt->execute();
                            $categoryResult = $stmt->get_result();
                            $category = $categoryResult->fetch_assoc();
                            $stmt->close();

                            if ($category) {
                                // Fetch products for this category
                                $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
                                $stmt->bind_param("s", $args['name']);
                                $stmt->execute();
                                $productResult = $stmt->get_result();
                                $products = [];
                                while ($row = $productResult->fetch_assoc()) {
                                    // Fetch each product's gallery, attributes, and prices as before
                                    $row['gallery'] = (new self())->fetchProductGallery($row['id']);
                                    $row['attributes'] = (new self())->fetchProductAttributes($row['id']);
                                    $row['prices'] = (new self())->fetchProductPrices($row['id']);
                                    $products[] = $row;
                                }
                                $stmt->close();
                                $category['products'] = $products;
                            }

                            $conn->close();
                            return $category;
                        }
                    ],
                    'product' => [
                        'type' => $productType,
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::string())]
                        ],
                        'resolve' => function($rootValue, $args) {
                            $conn = self::getDatabaseConnection();
                            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                            $stmt->bind_param("s", $args['id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $product = $result->fetch_assoc();
                            $stmt->close();
                            $conn->close();
                            return $product;
                        }
                    ]
                ]
            ]);

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $rootValue = [];
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
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