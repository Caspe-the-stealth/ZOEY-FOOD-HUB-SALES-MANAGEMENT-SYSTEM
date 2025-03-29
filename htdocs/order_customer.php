<?php
session_start();
include 'db_connection.php'; // Include database connection

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Handle order submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);
    $payment_method = htmlspecialchars($_POST['payment_method']);

    $order_items = json_encode($_SESSION['cart']); // Store cart as JSON
    $total_price = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $_SESSION['cart']));

    $stmt = $conn->prepare("INSERT INTO orders (name, email, address, payment_method, items, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssd", $name, $email, $address, $payment_method, $order_items, $total_price);

    if ($stmt->execute()) {
        $_SESSION['cart'] = []; // Clear cart
        header("Location: order_success.php");
        exit();
    } else {
        $error_message = "Order failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="order-container">
    <h2>Place Your Order</h2>

    <?php if (isset($error_message)): ?>
        <p class="error"><?= $error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="order.php">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Address:</label>
        <textarea name="address" required></textarea>

        <label>Payment Method:</label>
        <select name="payment_method">
            <option value="Credit Card">Credit Card</option>
            <option value="PayPal">PayPal</option>
            <option value="Cash on Delivery">Cash on Delivery</option>
        </select>

        <h3>Order Summary</h3>
        <ul>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <li><?= $item['quantity'] . " x " . htmlspecialchars($item['name']) . " - $" . number_format($item['price'] * $item['quantity'], 2); ?></li>
            <?php endforeach; ?>
        </ul>

        <p class="total">Total: $<?= number_format($total_price, 2); ?></p>

        <button type="submit" name="place_order">Place Order</button>
    </form>
</div>

</body>
</html>
