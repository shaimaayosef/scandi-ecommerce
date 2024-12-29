<?php

namespace App\Models;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

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
        error_log('Received order data: ' . print_r($args, true));
        $conn = Database::getDatabaseConnection();
        // Extracting order data
        $orderData = $args['orderData'];
        $total = $orderData['total']; 
        $products = $orderData['products'];

        // Insert Order Data into MySQL Database
        $stmt = $conn->prepare("INSERT INTO orders (total_price) VALUES (?)");
        $stmt->bind_param("d", $total);
        $stmt->execute();
        $orderId = $stmt->insert_id;
        $stmt->close();

        // Insert Products Data
        foreach ($products as $product) {
            $stmt = $conn->prepare("INSERT INTO order_products (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issdi", $orderId, $product['id'], $product['name'], $product['price'], $product['quantity']);
            $stmt->execute();
            
        }
        $stmt->close();
        error_log('Order placed successfully. Order ID: ' . $orderId);
        return true;
    }
}