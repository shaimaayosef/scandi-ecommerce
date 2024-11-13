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
        $dbname = "scandi4ecommerce";
        
        $conn = new \mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            throw new RuntimeException("Connection failed: " . $conn->connect_error);
        }
        
        return $conn;
    }

    static public function handle() {
        try {
            // Define Product Type
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
                            $conn = self::getDatabaseConnection();
                            $stmt = $conn->prepare("SELECT image_url FROM product_gallery WHERE product_id = ?");
                            $stmt->bind_param("s", $product['id']);
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
                            $conn = self::getDatabaseConnection();
                            $stmt = $conn->prepare("SELECT * FROM product_prices WHERE product_id = ?");
                            $stmt->bind_param("s", $product['id']);
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
                    ]
                ]
            ]);

            // Query Type
            
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf(Type::string()),
                        'resolve' => function() {
                            $conn = self::getDatabaseConnection();
                            $result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL");
                            $categories = [];
                            while ($row = $result->fetch_assoc()) {
                                $categories[] = $row['category'];
                            }
                            $conn->close();
                            return $categories;
                        }
                    ],
                    'products' => [
                        'type' => Type::listOf($productType),
                        'args' => [
                            'category' => Type::string(),
                        ],
                        'resolve' => function ($root, $args) {
                            $conn = self::getDatabaseConnection();
                            $category = isset($args['category']) ? $args['category'] : null;
                            $sql = "SELECT * FROM products";
                            if ($category) {
                                $sql .= " WHERE category = ?";
                            }
                            $stmt = $conn->prepare($sql);
                            if ($category) {
                                $stmt->bind_param("s", $category);
                            }
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $products = [];
                            while ($row = $result->fetch_assoc()) {
                                $products[] = $row;
                            }
                            $stmt->close();
                            $conn->close();
                            return $products;
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