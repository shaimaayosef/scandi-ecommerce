<?php
// src/GraphQL/Queries/QueryType.php
namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\CategoryType;
use App\Controller\GraphQLController;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $categoryType = new CategoryType();

        $config = [
            'name' => 'Query',
            'fields' => [
                'categories' => [
                    'type' => Type::listOf($categoryType),
                    'resolve' => function() use ($categoryType) {
                        $conn = GraphQLController::getDatabaseConnection();
                        $result = $conn->query("SELECT * FROM categories");
                        $categories = [];
                        while ($row = $result->fetch_assoc()) {
                            $categories[] = $row;
                        }
                        $conn->close();
                        return $categories;
                    }
                ],
                // Add other query fields here
            ],
        ];

        parent::__construct($config);
    }
}
