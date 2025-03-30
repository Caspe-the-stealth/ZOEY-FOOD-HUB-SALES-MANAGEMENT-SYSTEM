<?php
include 'db_connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle ADD order logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_order'])) {
    $order_date   = $_POST['order_date'];
    $order_status = $_POST['order_status']; // e.g., Pending, Completed, or Cancelled
    $customer_id  = $_POST['customer_id'];
    $product_id   = $_POST['product_id'];
    $price        = $_POST['price'];
    
    // Using uppercase column names for orders table: Customer_ID and Product_ID
    $stmt = $conn->prepare("INSERT INTO orders (Order_Date, Order_Status, Customer_ID, Product_ID, Total_Amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiid", $order_date, $order_status, $customer_id, $product_id, $price);
    $stmt->execute();
    $stmt->close();
    header("Location: orders.php");
    exit;
}

// Fetch customers for dropdown (assuming the customers table has customer_id, first_name, last_name)
$customerQuery = "SELECT * FROM customers";
$customerResult = $conn->query($customerQuery);

// Fetch products for dropdown (assuming the products table has Product_ID, Name, Price)
$productQuery = "SELECT * FROM products";
$productResult = $conn->query($productQuery);

// Fetch all orders by joining orders with customers and products
// Note: orders table columns are Customer_ID and Product_ID (uppercase) while customers use customer_id.
$query = "SELECT o.Order_ID, o.Order_Date, o.Order_Status, 
                 c.first_name, c.last_name, 
                 p.Name AS ProductName, o.Total_Amount 
          FROM orders o 
          INNER JOIN customers c ON o.Customer_ID = c.customer_id 
          INNER JOIN products p ON o.Product_ID = p.Product_ID 
          ORDER BY o.Order_Date DESC";
$result = $conn->query($query);
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="orders.css">
    <style>
.main-content {
    display: flex;
    justify-content: flex-start; /* Align items to the left */
    align-items: flex-start;
    padding: 30px;
    gap: 20px; /* Space between form and table */
}

.form-container {
    flex-basis: 30%; /* Set form width */
    max-width: 350px;
}

.table-container {
    flex-grow: 1; /* Allow table to take remaining space */
}


    /* General Styles */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #121212;
    color: #E0E0E0;
    margin: 0;
    padding: 0;
}

/* Sidebar */
.sidebar {
    width: 250px;
    background: #1E1E1E;
    height: 100vh;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 20px;
    position: fixed;
    left: 0;
    top: 0;
    box-shadow: 3px 0 20px rgba(0, 0, 0, 0.4);
}

.sidebar .logo-container {
    text-align: center;
    margin-bottom: 30px;
}

.sidebar .logo {
    width: 150px;
    margin-bottom: 20px;
}

.sidebar h2 {
    font-size: 24px;
    color: #FFFFFF;
    margin: 0;
}

.sidebar ul {
    list-style: none;
    padding: 0;
    width: 100%;
}

.sidebar ul li a {
    display: flex;
    align-items: center;
    padding: 14px 25px;
    color: #CCCCCC;
    text-decoration: none;
    font-size: 18px;
    transition: 0.3s ease-in-out;
}

.sidebar ul li a:hover,
.sidebar ul li a.active {
    background: #333333;
    color: #FFFFFF;
    font-weight: bold;
}

.sidebar ul li a i {
    margin-right: 15px;
}

/* Main Content */
.main-content {
    margin-left: 270px;
    padding: 30px;
    width: calc(100% - 270px);
    background: #181818;
    min-height: 100vh;
}

/* Heading */
h2 {
    text-align: center;
    color: #F0F0F0;
    margin-bottom: 25px;
    font-size: 24px;
}

/* Links */
a {
    text-decoration: none;
    color: #4DA8DA;
    transition: 0.3s;
}

a:hover {
    color: #77D7F9;
    text-decoration: underline;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    background-color: #222;
    margin-bottom: 20px;
    border-radius: 8px;
    overflow: hidden;
}

thead th {
    background-color: #444;
    color: #FFFFFF;
    padding: 12px;
    text-align: left;
    font-size: 16px;
}

tbody td {
    padding: 12px;
    border-bottom: 1px solid #333;
    color: #E0E0E0;
}

tbody tr:nth-child(even) {
    background-color: #2A2A2A;
}

tbody tr:hover {
    background-color: #3A3A3A;
}

/* Action Buttons */
.action-icons {
    font-size: 18px;
    transition: 0.3s;
    cursor: pointer;
    margin-right: 10px;
}

.action-icons.edit {
    color: #4CAF50;
}

.action-icons.edit:hover {
    color: #66FF66;
}

.action-icons.delete {
    color: #E53935;
}

.action-icons.delete:hover {
    color: #FF6666;
}

/* Form Styling */
.form-container {
    background: #242424;
    padding: 20px;
    border-radius: 8px;
    max-width: 600px;
    margin: auto;
    box-shadow: 2px 2px 15px rgba(255, 255, 255, 0.1);
}

form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

input, textarea, select {
    width: 100%;
    padding: 10px;
    margin: 0;
    border: none;
    border-radius: 5px;
    background: #2D2D2D;
    color: #E0E0E0;
    font-size: 16px;
}

input:focus, textarea:focus, select:focus {
    outline: none;
    box-shadow: 0 0 5px rgba(77, 168, 218, 0.5);
}

button {
    background-color: #4DA8DA;
    color: #FFF;
    border: none;
    padding: 12px 18px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 16px;
    transition: 0.3s;
    width: 100%;
}

button:hover {
    background-color: #77D7F9;
}

/* Form Title */
h3.form-title {
    font-size: 20px;
    color: #F0F0F0;
    margin-bottom: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
    }

    .main-content {
        margin-left: 220px;
    }

    .form-container {
        max-width: 100%;
        padding: 15px;
    }

    h2 {
        font-size: 22px;
    }
}
</style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="images (1).png" alt="Zoey Food Hub Logo" style="width: 100px; height: 100px; border-radius: 50%;">
            <h2>ZOEY FOOD HUB</h2>
        </div>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="customers.php">Customers</a></li>
            <li><a href="product.php">Product</a></li>
            <li><a href="orders.php">Orders</a></li>
            <li><a href="sales.php">Sales Records</a></li>
            <li><a href="login.php">Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <!-- Left Side: Add Order Form -->
        <div class="form-container">
            <h2>Add Order</h2>
            <form action="orders.php" method="post">
                <label>Order Date:</label>
                <input type="date" name="order_date" required>
                
                <label>Order Status:</label>
                <select name="order_status" required>
                    <option value="">Select Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                
                <label>Select Customer:</label>
                <select name="customer_id" required>
                    <option value="">Select Customer</option>
                    <?php while ($cust = $customerResult->fetch_assoc()): ?>
                        <option value="<?= $cust['customer_id']; ?>">
                            <?= htmlspecialchars($cust['first_name'] . ' ' . $cust['last_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label>Select Product:</label>
                <select name="product_id" required>
                    <option value="">Select Product</option>
                    <?php while ($prod = $productResult->fetch_assoc()): ?>
                        <option value="<?= $prod['Product_ID']; ?>">
                            <?= htmlspecialchars($prod['Name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label>Total Amount:</label>
                <input type="number" name="price" step="0.01" placeholder="Price" required>
                <br><br>
                <button type="submit" name="add_order">Add Order</button>
            </form>
        </div>
        
        <!-- Right Side: Orders Table -->
        <div class="table-container">
            <h2>Orders List</h2>
            <table>
                <tr>
                    <th>Order Date</th>
                    <th>Customer</th>
                    <th>Product Name</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Order_Date']); ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                        <td><?= htmlspecialchars($row['ProductName']); ?></td>
                        <td>₱<?= number_format($row['Total_Amount'], 2); ?></td>
                        <td><?= htmlspecialchars($row['Order_Status']); ?></td>
                        <td>
                            <a href="edit_order.php?id=<?= $row['Order_ID']; ?>" class="edit-btn">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>