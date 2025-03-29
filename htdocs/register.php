<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connection.php'; 

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (isset($_POST['register'])) {
    // Debug: log the form data
    error_log("Register form submitted: " . print_r($_POST, true));

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $role = trim($_POST['role']);

    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($address) || empty($role)) {
        echo "Please fill in all fields.";
        exit;
    }

    // Check if email already exists
    $check_sql = "SELECT Email FROM users WHERE Email = ?";
    if ($stmt = $conn->prepare($check_sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Error: This email is already registered.";
            $stmt->close();
            exit;
        }
        $stmt->close();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $sql = "INSERT INTO users (Name, Email, Password, Role, Phone, Address) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssss", $name, $email, $hashed_password, $role, $phone, $address);
        
        if ($stmt->execute()) {
            echo "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="register.css">
</head>
<!-- ...existing PHP and head code... -->
<body>
    <div class="container">
        <!-- ...existing code (logo, heading, etc.)... -->
        <form action="register.php" method="POST">
            <table>
                <tr>
                    <td><label for="name">Full Name:</label></td>
                    <td><input type="text" id="name" name="name" required></td>
                </tr>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="email" id="email" name="email" required></td>
                </tr>
                <tr>
                    <td><label for="username">Username:</label></td>
                    <td><input type="text" id="username" name="username" required></td>
                </tr>
                <tr>
                    <td><label for="password">Password:</label></td>
                    <td><input type="password" id="password" name="password" required></td>
                </tr>
                <tr>
                    <td><label for="phone">Phone:</label></td>
                    <td><input type="text" id="phone" name="phone" required></td>
                </tr>
                <tr>
                    <td><label for="address">Address:</label></td>
                    <td><textarea id="address" name="address" required></textarea></td>
                </tr>
                <tr>
                    <td><label for="role">Role:</label></td>
                    <td>
                        <select id="role" name="role" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><button type="submit" name="register">Register</button></td>
                </tr>
            </table>
        </form>
        <!-- ...existing code (links, etc.)... -->
    </div>
    <!-- ...existing code... -->
</body>
</html>
