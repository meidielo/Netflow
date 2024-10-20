<?php
// Function to fetch all orders or specific orders based on criteria
function fetch_orders($conn, $criteria = [])
{
    $query = "SELECT orders.order_id, orders.order_time, orders.order_cost, orders.order_status, orders.full_name, 
                     order_products.product_name, order_products.product_cost 
              FROM orders
              JOIN order_products ON orders.order_id = order_products.order_id";

    $params = [];
    $conditions = [];

    // Add conditions based on search criteria
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

    // Add where conditions if any
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'order_time'; // Default sort by order_time
    $order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC'; // Default to ascending

    // Sort by product_name, then by order_id
    if ($sort_by === 'product_name') {
        $query .= " ORDER BY order_products.product_name $order, orders.order_id ASC";
    } elseif ($sort_by === 'product_cost') {
        $query .= " ORDER BY order_products.product_cost $order, orders.order_id ASC";
    } else {
        // Default sorting for other columns
        $query .= " ORDER BY $sort_by $order, orders.order_id ASC";
    }

    $stmt = $conn->prepare($query);

    // If parameters exist, bind them to the statement
    if (!empty($params)) {
        $types = str_repeat("s", count($params)); // Assuming all parameters are strings

        // Prepare the array for call_user_func_array
        $bind_names[] = $types;

        // Loop through the parameters and add them to the array as references
        foreach ($params as $key => $value) {
            $bind_name = 'bind' . $key;
            $$bind_name = $value;  // Create a variable with a dynamic name
            $bind_names[] = &$$bind_name; // Pass by reference
        }

        // Use call_user_func_array to bind parameters dynamically
        call_user_func_array([$stmt, 'bind_param'], $bind_names);
    }

    $stmt->execute();
    return $stmt->get_result();
}