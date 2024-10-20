<?php
// Start session
session_start();

// Include the database settings and create manager table script
include 'create_manager_table.php';

// Initialize error message and form data variables
$error_message = "";
$username = "";
$password = "";
$confirm_password = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and capture form data
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    // Check if all fields are filled
    if (!empty($username) && !empty($password) && !empty($confirm_password)) {
        // Validate username length (e.g., 3-20 characters) and format (alphanumeric)
        if (!preg_match('/^[a-zA-Z0-9]{3,20}$/', $username)) {
            $error_message = "Username must be 3-20 characters long and contain only letters and numbers.";
        }
        // Check password length (e.g., at least 8 characters) and complexity
        elseif (strlen($password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
        }
        // Check for password complexity (e.g., must contain letters, numbers, and special characters)
        elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[!@#$%^&*]/', $password)) {
            $error_message = "Password must contain at least one letter, one number, and one special character (!@#$%^&*).";
        }
        // Check if passwords match
        elseif ($password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        }
        // If all validations pass, proceed to account creation
        else {
            // Hash the password before storing it
            $hashed_password = md5($password);

            // Connect to the database
            $conn = mysqli_connect($host, $user, $pwd, $sql_db);

            // Check connection
            if (!$conn) {
                die("<p class='error'>Database connection failed: " . mysqli_connect_error() . "</p>");
            }

            // Check if username already exists
            $check_query = "SELECT username FROM managers WHERE username = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, 's', $username);
            mysqli_stmt_execute($check_stmt);
            $result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($result) > 0) {
                $error_message = "Username is already taken.";
            } else {
                // Prepare SQL statement to insert new manager account
                $query = "INSERT INTO managers (username, password) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'ss', $username, $hashed_password);

                // Execute the statement
                if (mysqli_stmt_execute($stmt)) {
                    // Account created successfully, redirect to login page
                    header("Location: manager_login.php?success=1");
                    exit();
                } else {
                    // Error in account creation
                    $error_message = "Error creating account. Please try again.";
                }

                // Close the statement
                mysqli_stmt_close($stmt);
            }

            // Close the connection
            mysqli_stmt_close($check_stmt);
            mysqli_close($conn);
        }
    } else {
        $error_message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Create Account">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Create Account</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="signup.php">
            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
            <input type="password" name="password" placeholder="Password" value="<?php echo htmlspecialchars($password); ?>" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" value="<?php echo htmlspecialchars($confirm_password); ?>" required>
            <button type="submit">Create Account</button>
        </form>
    </div>
</body>
</html>