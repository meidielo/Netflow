<?php
// Include database connection
include 'settings.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status'];

    // Validate the input
    if (!empty($order_id) && !empty($new_status)) {
        // Connect to the database
        $conn = new mysqli($host, $user, $pwd, $sql_db);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare and bind the update query
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        $stmt->bind_param('si', $new_status, $order_id);

        // Execute the query
        if ($stmt->execute()) {
            echo "Order status updated successfully.";
        } else {
            echo "Error updating record: " . $conn->error;
        }

        // Close the connection
        $stmt->close();
        $conn->close();
    } else {
        echo "Invalid input.";
    }

    // Redirect back to the manager page after processing
    header("Location: manager.php");
    exit();
}
?>
