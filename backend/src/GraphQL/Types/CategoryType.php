<?php
// src/GraphQL/Types/CategoryType.php
namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Category',
            'fields' => [
                'name' => Type::string(),
                // Add other fields as needed
            ],
        ];

        parent::__construct($config);
    }
}
