<?php
session_start();
include 'db_connection.php'; // Ensure this file correctly connects to MySQL

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validate input
    if (empty($email) || empty($password) || empty($role)) {
        $_SESSION['error'] = 'Please fill in all fields.';
        header("Location: admin_dashboard.php");
        exit;
    }

    // Query the database for the user
    $query = "SELECT * FROM users WHERE email = ? AND role = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Optionally set a success message
                $_SESSION['message'] = 'Login successful.';
                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
                    exit;
                } elseif ($user['role'] == 'customer') {
                    header("Location: customer_dashboard.php");
                    exit;
                }
            } else {
                $_SESSION['error'] = 'Invalid password.';
                header("Location: admin_dashboard.php");
                exit;
            }
        } else {
            $_SESSION['error'] = 'User not found or role mismatch.';
            header("Location: admin_dashboard.php");
            exit;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Database error. Please try again later.';
        header("Location: admin_dashboard.php");
        exit;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <img class="logo" src="logo.png" alt="Logo">
        <h2>Login</h2>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
            </select><br><br>
            <button type="submit">Login</button>
        </form>
        <br>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>
