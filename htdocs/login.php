<?php
session_start();
ob_start(); // Start output buffering
include 'db_connection.php';
ob_end_clean(); // Clear any output from db_connection.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $role = strtolower(trim($_POST['role']));

    // Check if all fields are filled
    if (empty($email) || empty($password) || empty($role)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: login.php");
        exit;
    }

    // Ensure role is either admin or customer
    if (!in_array($role, ['admin', 'customer'])) {
        $_SESSION['error'] = "Invalid role selected.";
        header("Location: login.php");
        exit;
    }

    // Fetch user from database
    $query = "SELECT id, email, password, role FROM users WHERE email = ? AND role = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $dbRole = strtolower($user['role']);

            // Debugging - Print fetched role
            echo "Role from DB: " . $dbRole . "<br>";
            echo "Hashed Password from DB: " . $user['password'] . "<br>";

            if (password_verify($password, $user['password'])) {
                // Successful login
                session_regenerate_id(true);
                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $dbRole;

                // Corrected role-based redirection
                if ($dbRole === 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($dbRole === 'customer') {
                    header("Location: menu.php");
                } else {
                    $_SESSION['error'] = "Invalid role!";
                    header("Location: login.php");
                }
                exit;
            } else {
                $_SESSION['error'] = "Incorrect password.";
            }
        } else {
            $_SESSION['error'] = "User not found or incorrect role.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error. Try again later.";
    }
    $conn->close();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Custom UI</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfcUSWUcPhTrxvU7aPv1O7WGN2j_ZskYB9MA&s" alt="Company Logo" class="logo">
        <h2>Login to Your Account</h2>

        <form action="login.php" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="customer">Customer</option>
                </select>
            </div>

            <button type="submit">Login</button>
        </form>

        <p class="footer-text">Don't have an account? <a href="register.php">Sign up</a></p>
    </div>
</div>

<style>
  /* Import Google Font */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body {
    background: #ffffff;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Full centering for login box */
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    width: 100%;
}

/* Stylish login box */
.login-box {
    background: #ffffff;
    padding: 40px;
    width: 100%;
    max-width: 400px; /* Ensures form is not too wide */
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    text-align: center;
    color: #000;
}

/* Logo styling */
.logo {
    width: 90px;
    height: auto;
    margin-bottom: 15px;
}

/* Titles */
h2 {
    margin-bottom: 10px;
    font-size: 22px;
    font-weight: 600;
    color: #222;
}

.subtitle {
    font-size: 14px;
    color: #666;
    margin-bottom: 20px;
}

/* Form styling */
.form-group {
    text-align: left;
    margin-bottom: 15px;
}

.form-group label {
    font-weight: 500;
    font-size: 14px;
    display: block;
    margin-bottom: 5px;
    color: #333;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    background: #f9f9f9;
    color: #333;
    outline: none;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #000;
}

/* Submit Button */
button[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #000;
    border: none;
    border-radius: 6px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
    transition: background 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #333;
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

.logo {
    width: 90px;  /* Adjust size as needed */
    height: 90px; /* Ensure equal width & height */
    border-radius: 50%; /* Makes it circular */
    object-fit: cover; /* Ensures image fits well */
    display: block;
    margin: 0 auto; /* Center the logo */
    border: 3px solid #000; /* Black border */
}
</style>

</body>
</html>
