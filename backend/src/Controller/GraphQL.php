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
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;
use Exception;
class GraphQL {
    private static  function getDatabaseConnection() {
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

            $productAttributeItems = new ObjectType([
                'name' => 'ProductAttributeItems',
                'fields' => [
                    'item_id' => ['type' => Type::string()],
                    'attribute_name' => ['type' => Type::string()],
                    'display_value' => ['type' => Type::string()],
                    'value' => ['type' => Type::string()],
                    'product_id' => ['type' => Type::string()],
                ]
            ]);

            $productAttributes = new ObjectType([
                'name' => 'ProductAttributes',
                'fields' => [
                    'id' => ['type' => Type::string()],
                    'attribute_name' => ['type' => Type::string()],
                    'items' => ['type' => Type::listOf($productAttributeItems),
                                'resolve' => function($productAttributes) {
                                    $conn = self::getDatabaseConnection();
                                    $stmt = $conn->prepare("SELECT * FROM product_attribute_items WHERE product_id = ? AND attribute_name = ?");
                                    $stmt->bind_param("ss", $productAttributes['product_id'], $productAttributes['attribute_name']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $productAttributeItems = [];
                                    while ($row = $result->fetch_assoc()) {
                                        $productAttributeItems[] = $row;
                                    }
                                    $stmt->close();
                                    $conn->close();
                                    return $productAttributeItems;
                                }],
                    'attribute_type' => ['type' => Type::string()],
                    'product_id' => ['type' => Type::string()],
                ]
            ]);
            $productGallery = new ObjectType([
                'name' => 'ProductGallery',
                'fields' => [
                    'product_id' => ['type' => Type::string()],
                    'image_url' => ['type' => Type::string()],
                ]
            ]);

            $productPrice = new ObjectType([
                'name' => 'ProductPrice',
                'fields' => [
                    'product_id' => ['type' => Type::string()],
                    'amount' => ['type' => Type::float()],
                    'currency_label' => ['type' => Type::string()],
                    'currency_symbol' => ['type' => Type::string()],
                ]
            ]);

            $product = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => ['type' => Type::string()],
                    'name' => ['type' => Type::string()],
                    'inStock' => ['type' => Type::boolean()],
                    'stock' => ['type' => Type::int()],
                    'gallery' => ['type' => Type::listOf($productGallery),
                                  'resolve' => function($product) {
                                        $conn = self::getDatabaseConnection();
                                        $stmt = $conn->prepare("SELECT * FROM product_gallery WHERE product_id = ?");
                                        $stmt->bind_param("s", $product['id']);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $productGallery = [];
                                        while ($row = $result->fetch_assoc()) {
                                            $productGallery[] = $row;
                                        }
                                        $stmt->close();
                                        $conn->close();
                                        return $productGallery;
                                    }],
                    'description' => ['type' => Type::string()],
                    'category' => ['type' => Type::string()],
                    'attributes' => ['type' => Type::listOf($productAttributes),
                                     'resolve' => function($product) {
                                        $conn = self::getDatabaseConnection();
                                        $stmt = $conn->prepare("SELECT * FROM product_attributes WHERE product_id = ?");
                                        $stmt->bind_param("s", $product['id']);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $productAttributes = [];
                                        while ($row = $result->fetch_assoc()) {
                                            $productAttributes[] = $row;
                                        }
                                        $stmt->close();
                                        $conn->close();
                                        return $productAttributes;
                                    }],
                    'price' => ['type' => Type::listOf($productPrice),
                                'resolve' => function($product) {
                                    $conn = self::getDatabaseConnection();
                                    $stmt = $conn->prepare("SELECT * FROM product_prices WHERE product_id = ?");
                                    $stmt->bind_param("s", $product['id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $productPrices = [];
                                    while ($row = $result->fetch_assoc()) {
                                        $productPrices[] = $row;
                                    }
                                    $stmt->close();
                                    $conn->close();
                                    return $productPrices;
                                }
                            ],
                    'brand' => ['type' => Type::string()],
                    'category_id' => ['type' => Type::int()],
                ]
            ]);

            $categoryType = new ObjectType([
                'name' => 'Category',
                'fields' => [
                    'name' => ['type' => Type::string()],
                    'products' => ['type' => Type::listOf($product),
                                   'resolve' => function($category) {
                                        $conn = self::getDatabaseConnection();
                                        if ($category['name'] === 'all') {
                                            $stmt = $conn->prepare("SELECT * FROM products");
                                        } else {
                                            $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
                                            $stmt->bind_param("s", $category['name']);
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
                                    }],
                ]
            ]);

            // Define OrderItemType
            $orderItemType = new ObjectType([
                'name' => 'OrderItem',
                'fields' => [
                    'product_id' => ['type' => Type::nonNull(Type::string())],
                    'quantity' => ['type' => Type::nonNull(Type::int())],
                    'price' => ['type' => Type::nonNull(Type::float())],
                ]
            ]);

            // Define OrderType with name and phone_number
            $orderType = new ObjectType([
                'name' => 'Order',
                'fields' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'name' => ['type' => Type::nonNull(Type::string())],
                    'phone_number' => ['type' => Type::nonNull(Type::string())],
                    'total' => ['type' => Type::nonNull(Type::float())],
                    'created_at' => ['type' => Type::nonNull(Type::string())],
                    'items' => ['type' => Type::listOf($orderItemType)],
                ]
            ]);

            // Define OrderInputType with name and phone_number
            $orderItemInputType = new InputObjectType([
                'name' => 'OrderItemInput',
                'fields' => [
                    'product_id' => ['type' => Type::nonNull(Type::string())],
                    'quantity' => ['type' => Type::nonNull(Type::int())],
                    'price' => ['type' => Type::nonNull(Type::float())],
                ]
            ]);

            $orderInputType = new InputObjectType([
                'name' => 'OrderInput',
                'fields' => [
                    'name' => ['type' => Type::nonNull(Type::string())],
                    'phone_number' => ['type' => Type::nonNull(Type::string())],
                    'items' => ['type' => Type::nonNull(Type::listOf($orderItemInputType))],
                    'total' => ['type' => Type::nonNull(Type::float())],
                ]
            ]);


            
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf($categoryType),
                        'resolve' => function() {
                            $conn = self::getDatabaseConnection();
                            $stmt = $conn->prepare("SELECT * FROM categories");
                            if (!$stmt) {
                                throw new RuntimeException("Prepare failed: " . $conn->error);
                            }
                            if (!$stmt->execute()) {
                                throw new RuntimeException("Execute failed: " . $stmt->error);
                            }
                            $result = $stmt->get_result();
                            if (!$result) {
                                throw new RuntimeException("Getting result set failed: " . $stmt->error);
                            }
                            $categories = [];
                            while ($row = $result->fetch_assoc()) {
                                $categories[] = $row;
                            }
                            $stmt->close();
                            $conn->close();
                            return $categories;
                        }
                    ],
                    'product' => [
                        'type' => $product, // Returns a single Product type
                        'args' => [
                            'id' => Type::nonNull(Type::string()) // Argument 'id' is required
                        ],
                        'resolve' => function($root, $args) {
                            $conn = self::getDatabaseConnection();
                            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                            $stmt->bind_param("s", $args['id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $product = $result->fetch_assoc();
                            $stmt->close();
                            $conn->close();

                            if (!$product) {
                                throw new RuntimeException("Product with id {$args['id']} not found.");
                            }

                            return $product;
                        }
                    ],

                    'category' => [
                        'type' => $categoryType,
                        'args' => [
                            'name' => Type::nonNull(Type::string())
                        ],
                        'resolve' => function($root, $args) {
                            $conn = self::getDatabaseConnection();
                            $stmt = $conn->prepare("SELECT * FROM categories WHERE name = ?");
                            if (!$stmt) {
                                throw new RuntimeException("Prepare failed: " . $conn->error);
                            }
                            $stmt->bind_param("s", $args['name']);
                            if (!$stmt->execute()) {
                                throw new RuntimeException("Execute failed: " . $stmt->error);
                            }
                            $result = $stmt->get_result();
                            if (!$result) {
                                throw new RuntimeException("Getting result set failed: " . $stmt->error);
                            }
                            $category = $result->fetch_assoc();
                            $stmt->close();
                            $conn->close();

                            if (!$category) {
                                throw new RuntimeException("Category with name '{$args['name']}' not found.");
                            }

                            // Fetch products for this category
                            $category['products'] = (function($categoryName) {
                                $conn = self::getDatabaseConnection();
                                $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
                                if (!$stmt) {
                                    throw new RuntimeException("Prepare failed: " . $conn->error);
                                }
                                $stmt->bind_param("s", $categoryName);
                                if (!$stmt->execute()) {
                                    throw new RuntimeException("Execute failed: " . $stmt->error);
                                }
                                $result = $stmt->get_result();
                                if (!$result) {
                                    throw new RuntimeException("Getting result set failed: " . $stmt->error);
                                }
                                $products = [];
                                while ($row = $result->fetch_assoc()) {
                                    $products[] = $row;
                                }
                                $stmt->close();
                                $conn->close();
                                return $products;
                            })($args['name']);

                            return $category;
                        }
                    ],
                    'order' => [
                            'type' => $orderType,
                            'args' => [
                                'id' => Type::nonNull(Type::int())
                            ],
                            'resolve' => function($root, $args) {
                                $conn = self::getDatabaseConnection();
                                $order_id = $args['id'];

                                // Fetch order
                                $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
                                if (!$stmt) {
                                    throw new RuntimeException("Prepare failed: " . $conn->error);
                                }
                                $stmt->bind_param("i", $order_id);
                                if (!$stmt->execute()) {
                                    throw new RuntimeException("Execute failed: " . $stmt->error);
                                }
                                $result = $stmt->get_result();
                                $order = $result->fetch_assoc();
                                $stmt->close();

                                if (!$order) {
                                    throw new RuntimeException("Order with id {$order_id} not found.");
                                }

                                // Fetch order items
                                $stmt = $conn->prepare("SELECT product_id, quantity, price FROM order_items WHERE order_id = ?");
                                if (!$stmt) {
                                    throw new RuntimeException("Prepare failed: " . $conn->error);
                                }
                                $stmt->bind_param("i", $order_id);
                                if (!$stmt->execute()) {
                                    throw new RuntimeException("Execute failed: " . $stmt->error);
                                }
                                $result = $stmt->get_result();
                                $order_items = [];
                                while ($row = $result->fetch_assoc()) {
                                    $order_items[] = $row;
                                }
                                $stmt->close();
                                $conn->close();

                                // Attach items to order
                                $order['items'] = $order_items;

                                return $order;
                            }
                        ],
                        
                ]
            ]);

           $mutationType = new ObjectType([
                            'name' => 'Mutation',
                            'fields' => [
                                'createOrder' => [
                                    'type' => $orderType,
                                    'args' => [
                                        'input' => ['type' => Type::nonNull($orderInputType)]
                                    ],
                                    'resolve' => function($root, $args) {
                                        $conn = self::getDatabaseConnection();
                                        $input = $args['input'];
                                        $name = $input['name'];
                                        $phone_number = $input['phone_number'];
                                        $total = $input['total'];
                                        $items = $input['items'];

                                        // Begin Transaction
                                        $conn->begin_transaction();

                                        try {
                                            // Insert into orders table with name and phone_number
                                            $stmt = $conn->prepare("INSERT INTO orders (name, phone_number, total, created_at) VALUES (?, ?, ?, NOW())");
                                            if (!$stmt) {
                                                throw new RuntimeException("Prepare failed: " . $conn->error);
                                            }
                                            $stmt->bind_param("sd", $name, $phone_number, $total);
                                            if (!$stmt->execute()) {
                                                throw new RuntimeException("Execute failed: " . $stmt->error);
                                            }
                                            $order_id = $stmt->insert_id;
                                            $stmt->close();

                                            // Insert into order_items table
                                            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                                            if (!$stmt) {
                                                throw new RuntimeException("Prepare failed: " . $conn->error);
                                            }

                                            foreach ($items as $item) {
                                                $product_id = $item['product_id'];
                                                $quantity = $item['quantity'];
                                                $price = $item['price'];
                                                $stmt->bind_param("isid", $order_id, $product_id, $quantity, $price);
                                                if (!$stmt->execute()) {
                                                    throw new RuntimeException("Execute failed: " . $stmt->error);
                                                }
                                            }
                                            $stmt->close();

                                            // Commit Transaction
                                            $conn->commit();

                                            // Fetch the created order to return
                                            $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
                                            if (!$stmt) {
                                                throw new RuntimeException("Prepare failed: " . $conn->error);
                                            }
                                            $stmt->bind_param("i", $order_id);
                                            if (!$stmt->execute()) {
                                                throw new RuntimeException("Execute failed: " . $stmt->error);
                                            }
                                            $result = $stmt->get_result();
                                            $order = $result->fetch_assoc();
                                            $stmt->close();

                                            // Fetch order items
                                            $stmt = $conn->prepare("SELECT product_id, quantity, price FROM order_items WHERE order_id = ?");
                                            if (!$stmt) {
                                                throw new RuntimeException("Prepare failed: " . $conn->error);
                                            }
                                            $stmt->bind_param("i", $order_id);
                                            if (!$stmt->execute()) {
                                                throw new RuntimeException("Execute failed: " . $stmt->error);
                                            }
                                            $result = $stmt->get_result();
                                            $order_items = [];
                                            while ($row = $result->fetch_assoc()) {
                                                $order_items[] = $row;
                                            }
                                            $stmt->close();
                                            $conn->close();

                                            // Attach items to order
                                            $order['items'] = $order_items;

                                            return $order;

                                        } catch (Exception $e) {
                                            // Rollback Transaction on Error
                                            $conn->rollback();
                                            $conn->close();
                                            throw new RuntimeException("Transaction failed: " . $e->getMessage());
                                        }
                                    }
                                ]
                            ]
                        ]);

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)     
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