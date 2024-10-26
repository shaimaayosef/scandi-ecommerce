<?php

namespace App\Controller;
include __DIR__ . '/../../config/config.php';
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

class GraphQL {
    static public function handle() {
        global $conn; 
        $productType = new ObjectType([
            'name' => 'Product',
            'fields' => [
                'id' => Type::nonNull(Type::int()),
                'name' => Type::string(),
                'price' => Type::float(),
                'description' => Type::string(),
            ],
        ]);

        // Define Query Type
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'products' => [
                    'type' => Type::listOf($productType),
                    'resolve' => function($rootValue, $args) use ($conn) {
                        $sql = "SELECT * FROM products"; // Fetch all products
                        $result = $conn->query($sql);
                        if (!$result) {
                            throw new RuntimeException('Database query failed: ' . $conn->error);
                        }
                        return $result->fetch_all(MYSQLI_ASSOC); // Return as associative array
                    },
                ],
            ],
        ]);

        // Define Input Type for Adding Product
        $productInputType = new InputObjectType([
            'name' => 'ProductInput',
            'fields' => [
                'name' => Type::nonNull(Type::string()),
                'price' => Type::nonNull(Type::float()),
                'description' => Type::string(),
            ],
        ]);

        // Define Mutation Type
        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'addProduct' => [
                    'type' => $productType,
                    'args' => [
                        'input' => ['type' => $productInputType],
                    ],
                    'resolve' => function($rootValue, array $args) use ($conn) {
                        $input = $args['input'];
                        $stmt = $conn->prepare("INSERT INTO products (name, price, description) VALUES (?, ?, ?)");
                        if (!$stmt) {
                            throw new RuntimeException('Prepare failed: '.$conn->error);
                        }
                        $stmt->bind_param("sds", $input['name'], $input['price'], $input['description']);

                        if ($stmt->execute()) {
                            return [
                                'id' => $stmt->insert_id,
                                'name' => $input['name'],
                                'price' => $input['price'],
                                'description' => $input['description'],
                            ];
                        }

                        return null; // Return null if insertion fails
                    },
                ],
            ],
        ]);

        // Create Schema
        $schema = new Schema(
            (new SchemaConfig())
            ->setQuery($queryType)
            ->setMutation($mutationType)
        );

        // Handle Input
        $rawInput = file_get_contents('php://input');
        if ($rawInput === false) {
            throw new RuntimeException('Failed to get php://input');
        }

        $input = json_decode($rawInput, true);
        if (!isset($input['query'])) {
            throw new RuntimeException('No query provided');
        }

        $query = $input['query'];
        $variableValues = $input['variables'] ?? null;

        $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
        $output = $result->toArray();

        // Handle Errors
        if ($result->errors) {
            http_response_code(500); // Set HTTP response code to 500 for server errors
            $output = [
                'error' => [
                    'message' => implode(', ', array_map(static function ($error) {
                        return $error->getMessage();
                    }, $result->errors)),
                ],
            ];
        }

        // Send Response
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($output);
    }
}
