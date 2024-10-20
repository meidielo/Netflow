<?php
// Start the session
session_start();

// Include database settings
include 'settings.php';

// Check if the order_id is provided
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Connect to the database
    $conn = new mysqli($host, $user, $pwd, $sql_db);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // First, check if the order has a "PENDING" status
    $check_status_query = "SELECT order_status FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($check_status_query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        if ($order['order_status'] === 'PENDING') {
            // If the order is pending, allow deletion
            $delete_order_query = "DELETE FROM orders WHERE order_id = ?";
            $delete_stmt = $conn->prepare($delete_order_query);
            $delete_stmt->bind_param("i", $order_id);

            if ($delete_stmt->execute()) {
                // Successfully deleted, redirect to the manager page
                header("Location: manager.php?cancel_success=1");
                exit();
            } else {
                echo "Error deleting order: " . $conn->error;
            }

            $delete_stmt->close();
        } else {
            // If the order is not pending, don't allow deletion
            header("Location: manager.php?error=cannot_cancel");
            exit();
        }
    } else {
        // Order not found
        header("Location: manager.php?error=order_not_found");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect to the manager page if no order_id is provided
    header("Location: manager.php");
    exit();
}
?>
