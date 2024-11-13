<?php

$servername = "localhost";
$username = "scandiAdmin";
$password = "1234";
$dbname = "scandi4ecommerce";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Disable foreign key checks to allow deleting from tables with foreign key constraints
$conn->query('SET FOREIGN_KEY_CHECKS = 0');

// Array of table names to clear
$tables = [
    'product_gallery',
    'product_attribute_items',
    'product_attributes',
    'product_prices',
    'products',
    'categories'
];

// Clear all tables
foreach ($tables as $table) {
    $result = $conn->query("TRUNCATE TABLE $table");
    if ($result) {
        echo "Table $table cleared successfully.\n";
    } else {
        echo "Error clearing table $table: " . $conn->error . "\n";
    }
}

// Re-enable foreign key checks
$conn->query('SET FOREIGN_KEY_CHECKS = 1');

// Read JSON file
$jsonData = file_get_contents('data.json');
$data = json_decode($jsonData, true);

// Log total number of products in JSON
echo "Total products in JSON: " . count($data['data']['products']) . "\n";

// Insert categories
$stmtCategory = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
foreach ($data['data']['categories'] as $category) {
    $stmtCategory->bind_param("s", $category['name']);
    $stmtCategory->execute();
    echo "Inserted category: {$category['name']}\n";
}
$stmtCategory->close();

// Insert products and related data
$stmtProduct = $conn->prepare("INSERT INTO products (id, name, inStock, stok, description, category, brand, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmtPrice = $conn->prepare("INSERT INTO product_prices (product_id, amount, currency_label, currency_symbol) VALUES (?, ?, ?, ?)");
$stmtAttr = $conn->prepare("INSERT INTO product_attributes (product_id, attribute_name, attribute_type) VALUES (?, ?, ?)");
$stmtAttrItem = $conn->prepare("INSERT INTO product_attribute_items (product_id, attribute_name, display_value, value, item_id) VALUES (?, ?, ?, ?, ?)");
$stmtGallery = $conn->prepare("INSERT INTO product_gallery (product_id, image_url) VALUES (?, ?)");

foreach ($data['data']['products'] as $index => $product) {
    echo "Processing product " . ($index + 1) . " of " . count($data['data']['products']) . "\n";
    
    // Insert product
    $stmtProduct->bind_param("ssisss", $product['id'], $product['name'], $product['inStock'], $product['stok'], $product['description'], $product['category'], $product['brand'], $product['category_id']);
    if (!$stmtProduct->execute()) {
        echo "Error inserting product: " . $stmtProduct->error . "\n";
    }
    
    // Insert price
    foreach ($product['prices'] as $price) {
        $stmtPrice->bind_param("sdss", $product['id'], $price['amount'], $price['currency']['label'], $price['currency']['symbol']);
        if (!$stmtPrice->execute()) {
            echo "Error inserting price: " . $stmtPrice->error . "\n";
        }
    }
    
    // Insert attributes and attribute items
    foreach ($product['attributes'] as $attribute) {
        $stmtAttr->bind_param("sss", $product['id'], $attribute['name'], $attribute['type']);
        if (!$stmtAttr->execute()) {
            echo "Error inserting attribute: " . $stmtAttr->error . "\n";
        }
        
        foreach ($attribute['items'] as $item) {
            $stmtAttrItem->bind_param("sssss", $product['id'], $attribute['name'], $item['displayValue'], $item['value'], $item['id']);
            if (!$stmtAttrItem->execute()) {
                echo "Error inserting attribute item: " . $stmtAttrItem->error . "\n";
            }
        }
    }
    
    // Insert gallery images
    foreach ($product['gallery'] as $imageUrl) {
        $stmtGallery->bind_param("ss", $product['id'], $imageUrl);
        if (!$stmtGallery->execute()) {
            echo "Error inserting gallery image: " . $stmtGallery->error . "\n";
        }
    }
    
    echo "Finished processing product: {$product['name']} (ID: {$product['id']})\n";
}

$stmtProduct->close();
$stmtPrice->close();
$stmtAttr->close();
$stmtAttrItem->close();
$stmtGallery->close();

$conn->close();

echo "Total products processed: " . count($data['data']['products']) . "\n";
echo "Data insertion complete.\n";
?>