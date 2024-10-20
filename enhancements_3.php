<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Enhancements to the Web Application">
    <meta name="keywords" content="HTML, CSS, Enhancements">
    <meta name="author" content="Your Name">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhancements - NETFLOW</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <script src="scripts/enhancements.js" defer></script>
</head>

<body>
    <?php include 'header.inc'; ?> <!-- Including the header -->

    <section class="enh-section">
        <h1>Enhancements to the Specified Requirements</h1>
        <p>This page lists the additional enhancements that go beyond the requirements of the assignment. Each
            enhancement includes an explanation, the relevant code, and a link to where it is implemented on the site.
        </p>

        <div class="enhancement">
            <h2>Enhancement 1: Cart Functionality with Database Integration</h2>
            <p><strong>Description:</strong> The cart function has been enhanced to handle multiple products, each with
                their own selected features and comments. The cart dynamically updates as products are added, quantities
                are changed, and specific features or comments are updated. This enhancement uses both local storage for
                cart management and a MySQL database to store cart items on checkout.</p>

            <p><strong>Integration into PHP Cart System:</strong> The cart data stored in local storage is transferred to the server during checkout. Once the order is placed, the cart's product details (including product name, quantity, features, and comments) are inserted into the `order_products` table in the database, linked to the specific `order_id`.</p>

            <div class="content">
                <h3>PHP Code for Inserting Products into the Database:</h3>
                <pre><code>
// Include the database settings
include 'settings.php';

// Function to insert products into the `order_products` table
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
                </code></pre>
            </div>

            <div class="content">
                <h3>Checkout Logic for Handling Products and Inserting into the Database:</h3>
                <pre><code>
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

            // Redirect to receipt.php with the order ID after successful order and product insertions
            header("Location: receipt.php?orderID=$order_id");
            exit();
        }
    }
} else {
    echo "Error executing query: " . $stmt->error;
}
                </code></pre>
            </div>

            <p><strong>How it goes beyond the basics:</strong> This enhancement integrates local cart management with server-side data persistence. When users complete their checkout, all items and their details (product name, quantity, features, and comments) are saved into the database. This allows the cart data to be retained even after the session ends, enabling server-side management and retrieval of user orders.</p>
            <p><strong>Applied Example:</strong> <a href="payment.php">View the enhanced cart on the Payment Page</a></p>
        </div>

        <div class="enhancement">
            <h2>Enhancement 2: Dynamic Sorting Functionality</h2>
            <p><strong>Description:</strong> I implemented dynamic sorting for various columns in the manager's order report. The sorting allows the manager to sort orders by different criteria, such as order number, order date, customer name, product name, product cost, total cost, and order status. Each column can be clicked to toggle between ascending and descending order, providing flexible data management.</p>
            <div class="content">
                <pre><code>
// Sorting Logic in PHP
function fetch_orders($conn, $criteria = []) {
    $query = "SELECT orders.order_id, orders.order_time, orders.order_cost, orders.order_status, orders.full_name, 
                     order_products.product_name, order_products.product_cost 
              FROM orders
              JOIN order_products ON orders.order_id = order_products.order_id";

    $params = [];
    $conditions = [];

    if (!empty($criteria['customer_name'])) {
        $conditions[] = "(orders.full_name LIKE ?)";
        $params[] = "%" . $criteria['customer_name'] . "%";
    }
    if (!empty($criteria['product_name'])) {
        $conditions[] = "(order_products.product_name LIKE ?)";
        $params[] = "%" . $criteria['product_name'] . "%";
    }
    if (!empty($criteria['status'])) {
        $conditions[] = "(orders.order_status = ?)";
        $params[] = $criteria['status'];
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'order_time'; 
    $order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC'; 

    if ($sort_by === 'product_name') {
        $query .= " ORDER BY order_products.product_name $order, orders.order_id ASC";
    } elseif ($sort_by === 'product_cost') {
        $query .= " ORDER BY order_products.product_cost $order, orders.order_id ASC";
    } else {
        $query .= " ORDER BY $sort_by $order, orders.order_id ASC";
    }

    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $types = str_repeat("s", count($params)); 
        $bind_names[] = $types;

        foreach ($params as $key => $value) {
            $bind_name = 'bind' . $key;
            $$bind_name = $value;  
            $bind_names[] = &$$bind_name; 
        }

        call_user_func_array([$stmt, 'bind_param'], $bind_names);
    }

    $stmt->execute();
    return $stmt->get_result();
}
                    </code></pre>
            </div>
            <p><strong>How it goes beyond the basics:</strong> This enhancement provides the manager with more flexibility to view and manage orders by sorting the data dynamically based on the manager's preferences. Sorting by product name or customer name goes beyond the standard functionality, making the system more intuitive and easy to use.</p>
            <p><strong>Applied Example:</strong> <a href="manager.php">View the sorting functionality on the Manager Order Report Page</a></p>
        </div>

        <div class="enhancement">
            <h2>Enhancement 3: Login, Signup, and Logout System</h2>
            <p><strong>Description:</strong> This enhancement implements a secure login, signup, and logout system for the manager’s panel. Managers can create an account using the signup page, log in using their credentials, and securely log out from the system. The password is hashed using MD5 for added security. Upon successful login, session management is used to maintain the manager's authenticated state.</p>
            <div class="content">
                <pre><code>
// Signup Logic (signup.php)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    if (!empty($username) && !empty($password) && !empty($confirm_password)) {
        if (strlen($password) >= 8 && $password === $confirm_password) {
            $hashed_password = md5($password);  // Hash the password
            // SQL to insert manager's data
            $query = "INSERT INTO managers (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ss', $username, $hashed_password);
            if ($stmt->execute()) {
                header("Location: manager_login.php?success=1");
            }
        }
    }
}

// Login Logic (manager_login.php)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $hashed_password = md5($password);  // Hash the password to match with DB
    // SQL to verify login credentials
    $query = "SELECT * FROM managers WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $username, $hashed_password);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['manager_logged_in'] = true;
        header("Location: manager.php");
    }
}

// Logout Logic (logout.php)
session_start();
session_destroy();
header("Location: manager_login.php");
exit();
                </code></pre>
            </div>
            <p><strong>How it goes beyond the basics:</strong> This enhancement uses session management to ensure secure authentication, prevents unauthorized access to the manager's dashboard, and includes secure password handling with hashing. The system also validates form inputs to prevent common errors like mismatched passwords during signup.</p>
            <p><strong>Applied Example:</strong> <a href="manager_login.php">View the login, signup, and logout system</a></p>
        </div>
    </section>

    <section class="references">
    <h2>References</h2>
    <ul>
        <li>MD5 hashing for password security was inspired by examples found on <a href="https://www.php.net/manual/en/function.md5.php" target="_blank">PHP.net</a>.</li>
        <li>Session management and secure login practices were referenced from <a href="https://www.php.net/manual/en/function.session-start.php" target="_blank">PHP Session Documentation</a>.</li>
        <li>Validation techniques for secure form inputs were inspired by <a href="https://owasp.org/www-community/attacks/Input_Validation" target="_blank">OWASP Input Validation Guidelines</a>.</li>
        <li>The dynamic sorting algorithm was adapted from general MySQL query sorting practices, with additional insights from <a href="https://www.w3schools.com/sql/sql_orderby.asp" target="_blank">W3Schools SQL ORDER BY</a>.</li>
    </ul>
    </section>

    <?php include 'footer.inc'; ?> <!-- Including the footer -->
</body>

</html>