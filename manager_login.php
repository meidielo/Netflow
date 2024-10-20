<?php
// Start session
session_start();

// Include the database settings and create manager table script
include 'create_manager_table.php';

// Initialize error message variable
$error_message = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and capture form data
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Validate inputs (server-side validation)
    if (empty($username)) {
        $error_message = "Username is required.";
    } elseif (strlen($username) < 5 || strlen($username) > 20) {
        $error_message = "Username must be between 5 and 20 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error_message = "Username can only contain letters, numbers, and underscores.";
    }

    if (empty($password)) {
        $error_message .= "<br>Password is required.";
    } elseif (strlen($password) < 8) {
        $error_message .= "<br>Password must be at least 8 characters long.";
    }

    // Only proceed if there are no errors
    if (empty($error_message)) {
        // Connect to the database
        $conn = mysqli_connect($host, $user, $pwd, $sql_db);

        // Check connection
        if (!$conn) {
            die("<p class='error'>Database connection failed: " . mysqli_connect_error() . "</p>");
        }

        // Prepare SQL statement to select manager based on username
        $query = "SELECT * FROM managers WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if any result is found
        if (mysqli_num_rows($result) > 0) {
            $manager = mysqli_fetch_assoc($result);

            // Verify the password
            if (md5($password) === $manager['password']) {
                // Set session variables for the manager
                $_SESSION['manager_logged_in'] = true;
                $_SESSION['manager_username'] = $username;

                // Redirect to manager dashboard or order management page
                header("Location: manager.php");
                exit();
            } else {
                // Incorrect password
                $error_message = "Incorrect username or password.";
            }
            
            if (password_verify($password, $manager['password'])) {
                // Set session variables for the manager
                $_SESSION['manager_logged_in'] = true;
                $_SESSION['manager_username'] = $username;

                // Redirect to manager dashboard or order management page
                header("Location: manager.php");
                exit();
            } else {
                // Incorrect password
                $error_message = "Incorrect username or password.";
            }
        } else {
            // No such manager found
            $error_message = "Incorrect username or password.";
        }

        // Close the connection
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Manager Login">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Manager Login</h2>

        <!-- Display Server-Side Error Message if present -->
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="manager_login.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <!-- Create Account Text Link -->
        <div class="create-account-link">
            Don't have an account? <a href="signup.php">Create one</a>
        </div>
    </div>
</body>
</html>