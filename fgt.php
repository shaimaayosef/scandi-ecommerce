<?php
namespace App\Controller;
include __DIR__ . '/../../config/config.php';
use GraphQL\GraphQL as GraphQLBase;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

// GraphQL Schema
$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'categories' => [
            'type' => Type::listOf(new ObjectType([
                'name' => 'Category',
                'fields' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'name' => ['type' => Type::nonNull(Type::string())],
                ]
            ])),
            'resolve' => function ($root, $args) {
                // Implement category fetching logic
            }
        ],
        'products' => [
            'type' => Type::listOf(new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => ['type' => Type::nonNull(Type::string())],
                    'name' => ['type' => Type::nonNull(Type::string())],
                    'inStock' => ['type' => Type::boolean()],
                    'description' => ['type' => Type::string()],
                    'category' => ['type' => Type::string()],
                    'brand' => ['type' => Type::string()],
                    'gallery' => ['type' => Type::listOf(Type::string())],
                    'attributes' => [
                        'type' => Type::listOf(new ObjectType([
                            'name' => 'Attribute',
                            'fields' => [
                                'id' => ['type' => Type::nonNull(Type::string())],
                                'name' => ['type' => Type::nonNull(Type::string())],
                                'type' => ['type' => Type::nonNull(Type::string())],
                                'items' => ['type' => Type::listOf(new ObjectType([
                                    'name' => 'AttributeItem',
                                    'fields' => [
                                        'displayValue' => ['type' => Type::nonNull(Type::string())],
                                        'value' => ['type' => Type::nonNull(Type::string())],
                                        'id' => ['type' => Type::nonNull(Type::string())],
                                    ]
                                ]))]
                            ]
                        ])),
                        'resolve' => function ($product, $args) {
                            // Implement attribute fetching logic
                        }
                    ],
                    'prices' => [
                        'type' => Type::listOf(new ObjectType([
                            'name' => 'Price',
                            'fields' => [
                                'amount' => ['type' => Type::nonNull(Type::float())],
                                'currency' => ['type' => Type::nonNull(new ObjectType([
                                    'name' => 'Currency',
                                    'fields' => [
                                        'label' => ['type' => Type::nonNull(Type::string())],
                                        'symbol' => ['type' => Type::nonNull(Type::string())]
                                    ]
                                ]))]
                            ]
                        ]))
                    ]
                ]
            ])),
            'resolve' => function ($root, $args) {
                // Implement product fetching logic
            }
        ]
    ]
]);

$mutationType = new ObjectType([
    'name' => 'Mutation',
    'fields' => [
        'createOrder' => [
            'type' => new ObjectType([
                'name' => 'Order',
                'fields' => [
                    'id' => ['type' => Type::nonNull(Type::string())],
                    'items' => ['type' => Type::listOf(new ObjectType([
                        'name' => 'OrderItem',
                        'fields' => [
                            'productId' => ['type' => Type::nonNull(Type::string())],
                            'quantity' => ['type' => Type::nonNull(Type::int())]
                        ]
                    ]))],
                    'total' => ['type' => Type::nonNull(Type::float())]
                ]
            ]),
            'args' => [
                'items' => ['type' => Type::nonNull(Type::listOf(Type::nonNull(new InputObjectType([
                    'name' => 'OrderItemInput',
                    'fields' => [
                        'productId' => ['type' => Type::nonNull(Type::string())],
                        'quantity' => ['type' => Type::nonNull(Type::int())]
                    ]
                ]))))],
            ],
            'resolve' => function ($root, $args) {
                // Implement order creation logic
            }
        ]
    ]
]);

$schema = new Schema([
    'query' => $queryType,
    'mutation' => $mutationType
]);

// Abstract base classes
abstract class Model {
    protected $id;

    public function __construct($id) {
        $this->id = $id;
    }

    abstract public function toArray(): array;
}

abstract class CategoryModel extends Model {
    protected $name;

    public function __construct($id, $name) {
        parent::__construct($id);
        $this->name = $name;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}

abstract class ProductModel extends Model {
    protected $name;
    protected $inStock;
    protected $description;
    protected $category;
    protected $brand;
    protected $gallery;

    public function __construct($id, $name, $inStock, $description, $category, $brand, $gallery) {
        parent::__construct($id);
        $this->name = $name;
        $this->inStock = $inStock;
        $this->description = $description;
        $this->category = $category;
        $this->brand = $brand;
        $this->gallery = $gallery;
    }

    abstract public function getAttributes(): array;
    abstract public function getPrices(): array;
}

abstract class AttributeModel extends Model {
    protected $name;
    protected $type;

    public function __construct($id, $name, $type) {
        parent::__construct($id);
        $this->name = $name;
        $this->type = $type;
    }

    abstract public function getItems(): array;
}

// Concrete classes
class ClothingCategory extends CategoryModel {}

class TechCategory extends CategoryModel {}

class ClothingProduct extends ProductModel {
    public function getAttributes(): array {
        // Implement clothing-specific attribute logic
        return [];
    }

    public function getPrices(): array {
        // Implement clothing-specific price logic
        return [];
    }

    public function toArray(): array {
        return array_merge(parent::toArray(), [
            'attributes' => $this->getAttributes(),
            'prices' => $this->getPrices()
        ]);
    }
}

class TechProduct extends ProductModel {
    public function getAttributes(): array {
        // Implement tech-specific attribute logic
        return [];
    }

    public function getPrices(): array {
        // Implement tech-specific price logic
        return [];
    }

    public function toArray(): array {
        return array_merge(parent::toArray(), [
            'attributes' => $this->getAttributes(),
            'prices' => $this->getPrices()
        ]);
    }
}

class SizeAttribute extends AttributeModel {
    public function getItems(): array {
        // Implement size-specific item logic
        return [];
    }

    public function toArray(): array {
        return array_merge(parent::toArray(), [
            'items' => $this->getItems()
        ]);
    }
}

class ColorAttribute extends AttributeModel {
    public function getItems(): array {
        // Implement color-specific item logic
        return [];
    }

    public function toArray(): array {
        return array_merge(parent::toArray(), [
            'items' => $this->getItems()
        ]);
    }
}

// Resolver classes
class CategoryResolver {
    public function resolve() {
        // Implement category fetching logic using CategoryModel subclasses
    }
}

class ProductResolver {
    public function resolve() {
        // Implement product fetching logic using ProductModel subclasses
    }
}

class AttributeResolver {
    public function resolve($product) {
        // Implement attribute fetching logic using AttributeModel subclasses
    }
}

class OrderMutation {
    public function createOrder($items) {
        // Implement order creation logic
    }
}