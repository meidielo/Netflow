<?php
// Start the session
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // If the session variable is not set, redirect to an error page or display an error
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

// Include the database settings
include 'settings.php';
include 'insert_products.php';

// Turn off notice level error reporting
error_reporting(E_ALL & ~E_NOTICE);

// Function to sanitize input
function sanitise_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// Function to validate inputs according to specified rules
function validate_inputs($data, $type, $maxLength = null, $pattern = null)
{
    $data = sanitise_input($data);

    if ($maxLength && strlen($data) > $maxLength) {
        return false;
    }

    if ($pattern && !preg_match($pattern, $data)) {
        return false;
    }

    return true;
}

// Function to validate the card number based on the card type
function validateCardNumber($cardNumber, $cardType)
{
    $visaRegex = "/^4[0-9]{15}$/";           // Visa: 16 digits, starts with 4
    $masterCardRegex = "/^5[1-5][0-9]{14}$/"; // MasterCard: 16 digits, starts with 51-55
    $amexRegex = "/^3[47][0-9]{13}$/";        // Amex: 15 digits, starts with 34 or 37

    // Sanitize card number (remove any spaces or non-numeric characters)
    $cardNumber = preg_replace("/\D/", "", $cardNumber);

    // Validate based on card type
    $isValid = false;
    if ($cardType === 'Visa' && preg_match($visaRegex, $cardNumber)) {
        $isValid = true;
    } elseif ($cardType === 'MasterCard' && preg_match($masterCardRegex, $cardNumber)) {
        $isValid = true;
    } elseif ($cardType === 'Amex' && preg_match($amexRegex, $cardNumber)) {
        $isValid = true;
    }

    return $isValid;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Capture and sanitize form data
    $full_name = sanitise_input($_POST['fullName']);
    $email = sanitise_input($_POST['email']);
    $phone = sanitise_input($_POST['phone']);
    $address = sanitise_input($_POST['address']);
    $suburb = sanitise_input($_POST['suburb']);
    $state = sanitise_input($_POST['state']);
    $postcode = sanitise_input($_POST['postcode']);
    $preferred_contact = isset($_POST['contactMethod']) ? sanitise_input($_POST['contactMethod']) : "";

    $card_type = sanitise_input($_POST['cardType']);
    $card_name = sanitise_input($_POST['cardName']);
    $card_number = sanitise_input($_POST['cardNumber']);
    $card_expiry = sanitise_input($_POST['expiryDate']);
    $card_cvv = sanitise_input($_POST['cvv']);

    $order_cost = floatval(sanitise_input($_POST['totalPrice'])); // Assuming total price is calculated beforehand

    // Validate the inputs
    $enq_err = []; // Enquiry errors

    // Split full name into first and last names
    $name_parts = explode(" ", $full_name, 2);
    $first_name = isset($name_parts[0]) ? trim($name_parts[0]) : "";
    $last_name = isset($name_parts[1]) ? trim($name_parts[1]) : "";


    // Validate First Name
    if (empty($first_name)) {
        $enq_err[] = "First name is required.";
    } elseif (strlen($first_name) > 25) {
        $enq_err[] = "First name cannot exceed 25 characters.";
    } elseif (!preg_match('/^[a-zA-Z]+$/', $first_name)) {
        $enq_err[] = "First name must contain alphabetical characters only.";
    }

    // Validate Last Name
    if (empty($last_name)) {
        $enq_err[] = "Last name is required.";
    } elseif (strlen($last_name) > 25) {
        $enq_err[] = "Last name cannot exceed 25 characters.";
    } elseif (!preg_match('/^[a-zA-Z]+$/', $last_name)) {
        $enq_err[] = "Last name must contain alphabetical characters only.";
    }

    // Validate Email
    if (empty($email)) {
        $enq_err[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $enq_err[] = "Invalid email address format.";
    }

    // Validate Address
    if (empty($address)) {
        $enq_err[] = "Street address is required.";
    } elseif (strlen($address) > 40) {
        $enq_err[] = "Street address exceeds the maximum length of 40 characters.";
    }

    // Validate Suburb
    if (empty($suburb)) {
        $enq_err[] = "Suburb is required.";
    } elseif (strlen($suburb) > 20) {
        $enq_err[] = "Suburb exceeds the maximum length of 20 characters.";
    }

    // Validate State
    $valid_states = ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'];
    if (empty($state)) {
        $enq_err[] = "State is required.";
    } elseif (!in_array($state, $valid_states)) {
        $enq_err[] = "Invalid state selection. Please select a valid state.";
    }

    // Validate Postcode
    $statePostcodeRules = [
        'VIC' => ['3', '8'],
        'NSW' => ['1', '2'],
        'QLD' => ['4', '9'],
        'NT'  => ['0'],
        'WA'  => ['6'],
        'SA'  => ['5'],
        'TAS' => ['7'],
        'ACT' => ['0']
    ];
    $first_digit_of_postcode = isset($postcode[0]) ? $postcode[0] : null;

    if (empty($postcode)) {
        $enq_err[] = "Postcode is required.";
    } elseif (!preg_match('/^\d{4}$/', $postcode)) {
        $enq_err[] = "Postcode must be exactly 4 digits.";
    } elseif (!empty($state) && !in_array($first_digit_of_postcode, $statePostcodeRules[$state])) {
        $enq_err[] = "The postcode {$postcode} does not match the state {$state}.";
    }

    // Validate Phone Number
    if (empty($phone)) {
        $enq_err[] = "Phone number is required.";
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $enq_err[] = "Phone number must be exactly 10 digits.";
    }

    // Validate Preferred Contact Method
    if (empty($preferred_contact)) {
        $enq_err[] = "Please select a preferred contact method.";
    }

    $product_name = isset($_POST['products'][0]['productName']) ? sanitise_input($_POST['products'][0]['productName']) : '';
    $product_quantity = isset($_POST['products'][0]['productQuantity']) ? (int)sanitise_input($_POST['products'][0]['productQuantity']) : 0;
    $product_price = isset($_POST['products'][0]['productPrice']) ? floatval(sanitise_input($_POST['products'][0]['productPrice'])) : 0.0;
    $product_features = isset($_POST['products'][0]['productFeatures']) ? sanitise_input($_POST['products'][0]['productFeatures']) : '';
    $comments = isset($_POST['products'][0]['productComments']) ? sanitise_input($_POST['products'][0]['productComments']) : '';

    // Product Selection Validation (Select List)
    if (empty($product_name) || strtolower($product_name) === "select a product") {
        $enq_err[] = "Please select a valid product from the list.";
    }

    // Quantity Validation
    if ($product_quantity <= 0 || !is_numeric($product_quantity)) {
        $enq_err[] = "Product quantity must be a positive integer.";
    }

    // Product Features Validation (Checkboxes)
    if (empty($product_features) || $product_features === "No features selected") {
        $enq_err[] = "Please select at least one feature for the product.";
    }

    // Comments Field Validation (Textarea with a Placeholder, Max Length)
    if (!empty($comments) && strlen($comments) > 200) {
        $enq_err[] = "Comments should be a maximum of 200 characters.";
    }

    // If there are validation errors, display them and provide a link back
    if (!empty($enq_err)) {
        echo "<html><head><title>Form Errors</title></head><body>";
        echo "<h3>Errors found in form submission:</h3><ul>";
        foreach ($enq_err as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "<a href='javascript:history.go(-2)'>Go Back and Fix Errors</a>";
        echo "</body></html>";
        exit();
    }

    $full_address = "{$address}, {$suburb}, {$state}, {$postcode}";

    $payment_err = [];

    // Card type validation
    if (empty($card_type) || $card_type === "Select your card type") {
        $payment_err[] = "Please select a valid credit card type.";
    }

    // Cardholder name validation
    if (empty($card_name)) {
        $payment_err[] = "Cardholder name is required.";
    } elseif (!validate_inputs($card_name, 'text', 40, '/^[a-zA-Z\s]+$/')) {
        $payment_err[] = "Card name must be max 40 characters, alphabets, and spaces only.";
    }

    // Card number validation
    if (empty($card_number)) {
        $payment_err[] = "Card number is required.";
    } elseif (!validateCardNumber($card_number, $card_type)) {
        $payment_err[] = "Invalid card number for the given card type.";
    }

    // Expiry date validation
    if (empty($card_expiry)) {
        $payment_err[] = "Card expiry date is required.";
    } elseif (!preg_match('/^\d{2}\/\d{2}$/', $card_expiry)) {
        $payment_err[] = "Expiry date must be in MM/YY format.";
    } else {
        list($month, $year) = explode('/', $card_expiry);
        $month = (int)$month;
        $year = (int)$year;

        $current_year = (int)date('y'); // Last two digits of the current year
        $current_month = (int)date('m'); // Current month

        if ($month < 1 || $month > 12) {
            $payment_err[] = "Invalid expiry month (must be between 01 and 12).";
        } elseif ($year < $current_year || ($year == $current_year && $month < $current_month)) {
            $payment_err[] = "Card has expired.";
        }
    }

    // CVV validation
    if (empty($card_cvv)) {
        $payment_err[] = "CVV is required.";
    } elseif (!preg_match('/^\d{3}$/', $card_cvv)) {
        if (preg_match('/[a-zA-Z]/', $card_cvv)) {
            $payment_err[] = "CVV must contain numbers only, no alphabets are allowed.";
        } else {
            $payment_err[] = "CVV must be exactly 3 digits.";
        }
    }

    if (!empty($payment_err)) {
        echo "<html><head><title>Form Errors</title></head><body>";
        echo "<h3>Errors found in form submission:</h3><ul>";
        foreach ($payment_err as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo "<a href='javascript:history.back()'>Go Back and Fix Errors</a>";
        echo "</body></html>";
        exit();
    }

    // Debug print the total price value
    echo "Total Price from POST: ";
    var_dump($order_cost);

    if (is_nan($order_cost) || empty($order_cost)) {
        echo "Total price is NaN or empty, recalculating...";
        $order_cost = 0;

        // Handle single product enquiry data
        if (isset($_POST['productName']) && isset($_POST['productQuantity']) && isset($_POST['productPrice'])) {
            $product_name = sanitise_input($_POST['productName']);
            $product_quantity = (int) sanitise_input($_POST['productQuantity']);
            $product_price = floatval(sanitise_input($_POST['productPrice']));

            // Debug print product price and quantity for single product
            echo "Single Product Name: ";
            var_dump($product_name);
            echo "Single Product Quantity: ";
            var_dump($product_quantity);
            echo "Single Product Price: ";
            var_dump($product_price);

            $order_cost += $product_quantity * $product_price; // Accumulate the total cost for a single product

            echo "Order cost after single product calculation: ";
            var_dump($order_cost);
        }

        // Handle multiple products from cart
        if (isset($_POST['products']) && is_array($_POST['products'])) {
            foreach ($_POST['products'] as $product) {
                $product_quantity = (int) sanitise_input($product['productQuantity']);
                $product_price = floatval(sanitise_input($product['productPrice']));

                // Debug print product price and quantity for each product in cart
                echo "Product Quantity: ";
                var_dump($product_quantity);
                echo "Product Price: ";
                var_dump($product_price);

                $order_cost += $product_quantity * $product_price; // Accumulate the total cost
            }
        }

        echo "Recalculated Total Price: ";
        var_dump($order_cost);
    }

    // Payment details
    $order_status = 'PENDING'; // Default order status
    $order_time = date("Y-m-d H:i:s"); // Current date and time

    // Create connection
    $conn = new mysqli($host, $user, $pwd, $sql_db);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    // Check if the 'orders' table exists, if not create it
    $table_check_query = "CREATE TABLE IF NOT EXISTS `orders` (
        `order_id` int(11) NOT NULL AUTO_INCREMENT,
        `full_name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `full_address` varchar(255) NOT NULL,
        `phone` varchar(15) NOT NULL,
        `contact_method` varchar(50) NOT NULL,
        `card_type` enum('Visa', 'MasterCard', 'Amex') NOT NULL,
        `card_name` varchar(40) NOT NULL,
        `card_number` varchar(16) NOT NULL,
        `expiry_date` varchar(5) NOT NULL,
        `cvv` varchar(3) NOT NULL,
        `order_cost` decimal(10,2) NOT NULL,
        `order_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `order_status` enum('PENDING', 'FULFILLED', 'PAID', 'ARCHIVED') NOT NULL DEFAULT 'PENDING',
        PRIMARY KEY (`order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

    if ($conn->query($table_check_query) === TRUE) {
        echo "Orders table created or exists.<br>";
    } else {
        die("Error creating 'orders' table: " . $conn->error);
    }

    // Check if the 'order_products' table exists, if not create it
    $table_check_query_products = "CREATE TABLE IF NOT EXISTS `order_products` (
        `product_id` int(11) NOT NULL AUTO_INCREMENT,
        `order_id` int(11) NOT NULL,
        `product_name` varchar(100) NOT NULL,
        `product_quantity` int(11) NOT NULL,
        `product_features` varchar(255) DEFAULT NULL,
        `product_comments` text DEFAULT NULL,
        `product_cost` decimal(10,2) NOT NULL,
        PRIMARY KEY (`product_id`),
        KEY `order_id` (`order_id`),
        CONSTRAINT `order_products_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

    if ($conn->query($table_check_query_products) === TRUE) {
        echo "Order products table created or exists.<br>";
    } else {
        die("Error creating 'order_products' table: " . $conn->error);
    }

    // Insert order into `orders` table
    $sql = "INSERT INTO orders 
    (full_name, email, phone, full_address, card_type, card_name, card_number, expiry_date, cvv, order_cost, order_status, order_time, contact_method)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param(
            "sssssssssdsss",  // 13 types for 13 placeholders
            $full_name,
            $email,
            $phone,
            $full_address,
            $card_type,
            $card_name,
            $card_number,
            $card_expiry,
            $card_cvv,
            $order_cost,
            $order_status,
            $order_time,
            $preferred_contact
        );

        if ($stmt->execute()) {
            // Start the session if it isn't already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Set a session variable to track that the order was successfully processed
            $_SESSION['order_processed'] = true;
            $_SESSION['order_id'] = $order_id; // Save the order ID when the order is processed

            // Get the order ID for the newly created order
            $order_id = $stmt->insert_id;

            // Insert products into `order_products` table
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['products']) && is_array($_POST['products'])) {
                    insert_order_products($conn, $order_id, $_POST['products']);
                    // //debug
                    // echo "<pre>";
                    // print_r($_POST);
                    // echo "</pre>";
                    // exit();

                    // Redirect to receipt.php with the order ID after successful order and product insertions
                    header("Location: receipt.php?orderID=$order_id");
                    exit();
                }
            }
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }

    // Close the connection
    $conn->close();
}
