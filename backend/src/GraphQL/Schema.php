<?php
// src/GraphQL/Schema.php
namespace App\GraphQL;

use GraphQL\Type\Schema as GraphQLSchema;
use App\GraphQL\Queries\QueryType;

class Schema
{
    private static $schema = null;

    public static function getSchema(): GraphQLSchema
    {
        if (self::$schema === null) {
            $queryType = new QueryType();

            self::$schema = new GraphQLSchema([
                'query' => $queryType,
                // 'mutation' => ..., // Define mutations if needed
                // 'types' => [...], // Add additional types if necessary
            ]);
        }

        return self::$schema;
    }
}
