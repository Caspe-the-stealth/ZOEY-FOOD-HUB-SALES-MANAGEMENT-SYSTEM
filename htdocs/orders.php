<?php
include 'db_connection.php'; // Database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle DELETE order request
if (isset($_GET['delete'])) {
    $order_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE Order_ID = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    header("Location: orders.php");
    exit();
}

// Handle ADD Order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_order'])) {
    // Retrieve form values using the proper keys
    $total_amount = $_POST['total_amount'];
    $order_status = $_POST['order_status'];
    $payment_status = $_POST['payment_status'];
    // Make sure the input field's name is "delivery_address"
    $delivery_address = trim($_POST['delivery_address']);
    $user_id = $_POST['user_id']; // Expecting a valid user id

    // Check if User ID exists in users table to avoid foreign key constraint failure.
    $user_check = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $user_check->bind_param("i", $user_id);
    $user_check->execute();
    $user_result = $user_check->get_result();
    if ($user_result->num_rows == 0) {
        echo "Error: The User ID provided does not exist.";
        exit();
    }
    $user_check->close();

    // IMPORTANT: The Payment_Status value must match the column definition in your database.
    // For example, if your orders table defines Payment_Status as ENUM('Unpaid','Paid'),
    // then the option "Refunded" will cause a truncation error.
    // Either update your database column to allow "Refunded" (or other values) or adjust your form options.
    $stmt = $conn->prepare("INSERT INTO orders (Total_Amount, Order_Status, Payment_Status, Delivery_Address, User_ID) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("dsssi", $total_amount, $order_status, $payment_status, $delivery_address, $user_id);
    $stmt->execute();
    header("Location: orders.php");
    exit();
}

// Handle EDIT Order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_order'])) {
    $order_id = intval($_POST['order_id']);
    $total_amount = $_POST['total_amount'];
    $order_status = $_POST['order_status'];
    $payment_status = $_POST['payment_status'];
    $delivery_address = trim($_POST['delivery_address']);

    $stmt = $conn->prepare("UPDATE orders SET Total_Amount=?, Order_Status=?, Payment_Status=?, Delivery_Address=? WHERE Order_ID=?");
    $stmt->bind_param("dsssi", $total_amount, $order_status, $payment_status, $delivery_address, $order_id);
    $stmt->execute();
    header("Location: orders.php");
    exit();
}

// Fetch all orders
$query = "SELECT * FROM orders ORDER BY Order_Date DESC";
$result = $conn->query($query);
if (!$result) {
    die("Database query failed: " . $conn->error);
}


$payment_status = $_POST['payment_status'] ?? null;
if ($payment_status !== null) {
    $payment_status = trim($payment_status);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="orders.css">
</head>
<body>
<div class="sidebar">
    <div class="logo-container">
        <img src="logo.png" alt="Zoey Food Hub Logo" class="logo">
        <h2>ZOEY FOOD HUB</h2>
    </div>
    <ul>
        <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="product.php"><i class="fas fa-box"></i> Product</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales Records</a></li>
        <li><a href="#"><i class="fas fa-file-alt"></i> Reports</a></li>
        <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
        <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h2>Manage Orders</h2>

    <!-- Add Order Form -->
    <form action="orders.php" method="post">
        <input type="number" name="total_amount" step="0.01" placeholder="Total Amount" required>
        <select name="order_status">
            <option value="Pending">Pending</option>
            <option value="Processing">Processing</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
        </select>
        <select name="payment_status">
            <!-- Adjust these options to match your database column definition -->
            <option value="Unpaid">Unpaid</option>
            <option value="Paid">Paid</option>
            <option value="Refunded">Refunded</option>
        </select>
        <!-- Make sure the input name is "delivery_address" to match $_POST['delivery_address'] -->
        <input type="text" name="delivery_address" placeholder="Address" required>
        <input type="number" name="user_id" placeholder="User ID" required>
        <button type="submit" name="add_order">Add Order</button>
    </form>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Total Amount</th>
            <th>Order Status</th>
            <th>Payment Status</th>
            <th>Address</th>
            <th>User ID</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Order_ID']); ?></td>
                <td><?= htmlspecialchars($row['Order_Date']); ?></td>
                <td>₱<?= number_format($row['Total_Amount'], 2); ?></td>
                <td><?= htmlspecialchars($row['Order_Status']); ?></td>
                <td><?= htmlspecialchars($row['Payment_Status']); ?></td>
                <td><?= htmlspecialchars($row['Delivery_Address']); ?></td>
                <td><?= htmlspecialchars($row['User_ID']); ?></td>
                <td>
                    <a href="edit_order.php?id=<?= $row['Order_ID']; ?>" class="edit-btn">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>