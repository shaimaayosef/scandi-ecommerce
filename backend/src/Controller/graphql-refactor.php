<?php

namespace App\Controller;

require __DIR__ . '/../../vendor/autoload.php';

use App\Models\Database;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductAttributeModel;
use App\Models\OrderModel;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use RuntimeException;
use Throwable;

class GraphQL {
    static public function handle() {
        // Add CORS headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Handle preflight request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
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
                    ],
                    'categories' => [
                        'type' => Type::listOf($categoryModel->getGraphQLType()),
                        'resolve' => fn() => $categoryModel->resolveAll()
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