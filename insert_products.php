<?php
// Include the database settings
include 'settings.php';

// Function to insert products into `order_products` table
function insert_order_products($conn, $order_id, $products) {
    foreach ($products as $product) {
        $product_name = isset($product['productName']) ? sanitise_input($product['productName']) : '';
        $product_quantity = isset($product['productQuantity']) ? (int) sanitise_input($product['productQuantity']) : 0;
        $product_features = isset($product['productFeatures']) ? sanitise_input($product['productFeatures']) : null;
        $product_comments = isset($product['productComments']) ? sanitise_input($product['productComments']) : '';
        $product_cost = isset($product['productPrice']) ? floatval(sanitise_input($product['productPrice'])) : 0.0;

        $product_sql = "INSERT INTO order_products (order_id, product_name, product_quantity, product_features, product_comments, product_cost) VALUES (?, ?, ?, ?, ?, ?)";

        if ($product_stmt = $conn->prepare($product_sql)) {
            $product_stmt->bind_param(
                "isissd",  // 6 types for 6 placeholders
                $order_id,
                $product_name,
                $product_quantity,
                $product_features,
                $product_comments,
                $product_cost
            );

            if (!$product_stmt->execute()) {
                echo "Error inserting product: " . $product_stmt->error;
            }
            $product_stmt->close();
        } else {
            echo "Error preparing product query: " . $conn->error;
        }
    }
}
?>
