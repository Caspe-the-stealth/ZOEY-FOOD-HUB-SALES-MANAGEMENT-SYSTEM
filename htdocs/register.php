<?php
session_start();
ob_start(); // Start output buffering
include 'db_connection.php';
ob_end_clean(); // Clear any output from db_connection.php

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
    <title>Register | ZoeY Food Hub</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="container">
        <h2>Create an Account</h2>
        <p class="subtitle">Fill in your details to register</p>
        <form action="register.php" method="POST">
            <table>
                <tr>
                    <td><label for="name">Full Name:</label></td>
                    <td><input type="text" id="name" name="name" placeholder="John Doe" required></td>
                </tr>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="email" id="email" name="email" placeholder="john@example.com" required></td>
                </tr>
                <tr>
                    <td><label for="username">Username:</label></td>
                    <td><input type="text" id="username" name="username" placeholder="Username" required></td>
                </tr>
                <tr>
                    <td><label for="password">Password:</label></td>
                    <td><input type="password" id="password" name="password" placeholder="Enter password" required></td>
                </tr>
                <tr>
                    <td><label for="phone">Phone:</label></td>
                    <td><input type="text" id="phone" name="phone" placeholder="Your phone number" required></td>
                </tr>
                <tr>
                    <td><label for="address">Address:</label></td>
                    <td><textarea id="address" name="address" placeholder="Your address" required></textarea></td>
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
                    <td colspan="2">
                        <button type="submit" name="register">Register</button>
                    </td>
                </tr>
            </table>
        </form>
        <p class="footer-text">Already registered? <a href="login.php">Login here</a></p>
    </div>
    
    <style>
        /* Container styling */
        .container {
            width: 400px;
            margin: 20px auto;
            padding: 30px;
            border-radius: 10px;
            background: #f9f9f9;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            font-family: 'Poppins', sans-serif;
        }
        
        .logo {
            width: 80px;
            margin-bottom: 15px;
        }

        h2 {
            margin-bottom: 10px;
            font-size: 24px;
            color: #333;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        form {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table tr td {
            padding: 8px;
        }

        /* Input and select styling */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Button styling */
        button {
            width: 100%;
            padding: 12px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        button:hover {
            background: #333;
        }

        /* Footer text */
        .footer-text {
            margin-top: 15px;
            font-size: 13px;
            color: #555;
        }

        .footer-text a {
            color: #000;
            font-weight: bold;
            text-decoration: none;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 480px) {
            .container {
                width: 90%;
                padding: 15px;
            }
        }
    </style>
</body>
</html>