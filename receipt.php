<?php
// Start the session
session_start();


// Check if the session variable 'order_processed' is set
if (!isset($_SESSION['order_processed']) || $_SESSION['order_processed'] !== true) {
    // Show the error message with styled CSS from external file
    die('
        <html>
            <head>
                <link rel="stylesheet" type="text/css" href="styles/die.css">
                <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
                <meta http-equiv="refresh" content="3;url=index.php">
                <script src="scripts/countdown.js"></script>
            </head>
            <body>
                <div class="error-box">
                    <h1>Access Denied</h1>
                    <p>You are not authorized to access this page directly.</p>
                    <p>Redirecting you to the homepage in <span id="countdown">3</span> seconds...</p>
            </div>
            </body>
        </html>
    ');
}

// Include the database connection settings
include 'settings.php'; // Contains $host, $user, $pwd, $sql_db

// Check if the orderID is provided in the URL
if (!isset($_GET['orderID']) || empty($_GET['orderID'])) {
    echo "<p class='error'>Invalid order ID.</p>";
    exit();
}

// Sanitize the orderID from the URL
$orderID = htmlspecialchars($_GET['orderID'], ENT_QUOTES, 'UTF-8');

// Fetch the order details from the orders table
$query = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($query);
// Bind the orderID to the query
$stmt->bind_param('i', $orderID);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if any order is found
if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
} else {
    echo "<p class='error'>No order found for the given order ID.</p>";
    exit();
}

// Fetch the products related to this order
$product_query = "SELECT * FROM order_products WHERE order_id = ?";
$product_stmt = $conn->prepare($product_query);

// Bind the orderID to the product query
$product_stmt->bind_param('i', $orderID);

// Execute the product query
$product_stmt->execute();

// Get the result
$product_result = $product_stmt->get_result();

// Close the prepared statements
$stmt->close();
$product_stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Order Receipt">
    <meta name="keywords" content="Wi-Fi router, NETFLOW, enquiry">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Meidie Fei">
    <title>Order Receipt</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/receipt.css">
</head>

<body>
    <?php include 'header.inc'; ?>

    <div class="container payment-layout">
        <div class="receipt-left">
            <h1>Thank you for your purchase!</h1>
            <p>Your order will be processed within 24 hours during working days. We will notify you via email once your items have been shipped.</p>

            <hr>

            <h3>Billing address</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['full_address']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>

            <hr>

            <h3>Payment Information</h3>
            <p><strong>Card Type:</strong> <?php echo htmlspecialchars($order['card_type']); ?></p>
            <p><strong>Card Holder:</strong> <?php echo htmlspecialchars($order['card_name']); ?></p>
            <p><strong>Card Number:</strong> <?php echo htmlspecialchars($order['card_number']); ?></p>
            <p><strong>Expiry Date:</strong> <?php echo htmlspecialchars($order['expiry_date']); ?></p>
            <p><strong>CVV:</strong> <?php echo htmlspecialchars($order['cvv']); ?></p>

        </div>
        <div class="receipt-right">
            <h2>Order Summary</h2>
            <p><strong>Order Date:</strong> <?php echo date("d M Y", strtotime($order['order_time'])); ?></p>
            <p><strong>Order Number:</strong> <?php echo htmlspecialchars($orderID); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['contact_method']); ?></p>
            <div class="receipt-right">
                <?php if (mysqli_num_rows($product_result) > 0) : ?>
                    <?php
                    $productNames = [
                        'ASUS RT-BE92U' => 'router1',
                        'ASUS RT-BE88U' => 'router2',
                        'ROG Rapture GT-BE98' => 'router3'
                    ];
                    $product_count = mysqli_num_rows($product_result);
                    $i = 0;
                    while ($product = mysqli_fetch_assoc($product_result)) :
                        $i++; // Increment product counter
                        // Get the product name
                        $product_name = htmlspecialchars($product['product_name']);

                        // Check if the product name exists in the $productNames array
                        $product_identifier = isset($productNames[$product_name]) ? $productNames[$product_name] : 'default';

                        // Construct the image path
                        $product_image = "images/" . htmlspecialchars($product_identifier) . ".png";
                    ?>
                        <div class="product-details"> <!-- Flexbox for aligning image and text -->
                            <div class="product-content-wrapper">
                                <!-- Product Information -->
                                <div class="product-info">
                                    <p><?php echo htmlspecialchars($product['product_name']) . " (x" . htmlspecialchars($product['product_quantity']) . ")"; ?> - $<?php echo number_format($product['product_cost'], 2); ?></p>
                                    <p><strong>Features:</strong> <?php echo !empty($product['product_features']) ? htmlspecialchars($product['product_features']) : 'N/A'; ?></p>
                                    <p><strong>Comments:</strong> <?php echo !empty($product['product_comments']) ? htmlspecialchars($product['product_comments']) : 'N/A'; ?></p>
                                </div>

                                <!-- Product Image -->
                                <div class="product-image">
                                    <img src="<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="width: 100px; height: auto;">
                                </div>
                            </div>
                        </div>
                        <?php if ($i < $product_count) : ?>
                            <hr class="product-separator"> <!-- Separator line for all but last product -->
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p>No products found for this order.</p>
                <?php endif; ?>
            </div>


            <div class="order-total">
                <p><strong>Order Total:</strong> $<?php echo number_format($order['order_cost'], 2); ?></p>
            </div>
        </div>
    </div>
    <?php
    include 'footer.inc';
    unset($_SESSION['order_processed']);
    ?>
</body>

</html>