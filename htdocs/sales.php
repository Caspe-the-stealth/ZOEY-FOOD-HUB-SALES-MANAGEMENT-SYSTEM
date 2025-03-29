<?php
include 'db_connection.php';

// SQL query to fetch sales records without user_id and payment method
$sql = "SELECT 
    users.Name AS customer_name, 
    products.Name AS product_name, 
    order_items.quantity, 
    (order_items.quantity * products.Price) AS total_amount,
    payments.Payment_Status, 
    payments.Transaction_Date
FROM orders 
JOIN users ON orders.user_id = users.id 
JOIN order_items ON orders.order_id = order_items.order_id 
JOIN products ON order_items.product_id = products.Product_ID 
JOIN payments ON orders.order_id = payments.order_id
ORDER BY users.Name;";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Records</title>
    <link rel="stylesheet" href="sales.css">
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
        <li><a href="product.php"><i class="fas fa-users"></i> Product</a></li>
        <li><a href="orders.php"><i class="fas fa-chart-line"></i> Orders</a></li>
        <li><a href="#"><i class="fas fa-chart-line"></i> Sales</a></li>
        <li><a href="#"><i class="fas fa-file-alt"></i> Reports</a></li>
        <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
        <li><a href="login.php"><i class="fas fa-cogs"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h2>Sales Records</h2>
    <table>
        <tr>
            <th>Customer Name</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Total Amount</th>
            <th>Payment Status</th>
            <th>Transaction Date</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["customer_name"]) . "</td>
                        <td>" . htmlspecialchars($row["product_name"]) . "</td>
                        <td>" . htmlspecialchars($row["quantity"]) . "</td>
                        <td>$" . number_format($row["total_amount"], 2) . "</td>
                        <td>" . htmlspecialchars($row["Payment_Status"]) . "</td>
                        <td>" . htmlspecialchars($row["Transaction_Date"]) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No records found</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
