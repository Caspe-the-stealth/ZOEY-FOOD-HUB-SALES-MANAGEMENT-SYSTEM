<?php
include 'db_connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$customer_id = "";
$first_name = ""; 
$last_name  = "";
$email      = "";
$phone      = "";
$address    = "";
$form_action = "add_customer";

// Handle Edit Request
if (isset($_GET['edit'])) {
    $customer_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        $first_name = $customer['first_name'];
        $last_name  = $customer['last_name'];
        $email      = $customer['email'];
        $phone      = $customer['phone'];
        $address    = $customer['address'];
        $form_action = "edit_customer";
    }
    $stmt->close();
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $address    = trim($_POST['address']);
    
    if (isset($_POST['add_customer'])) {
        $stmt = $conn->prepare("INSERT INTO customers (first_name, last_name, email, phone, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $address);
    } elseif (isset($_POST['edit_customer'])) {
        $customer_id = intval($_POST['customer_id']);
        $stmt = $conn->prepare("UPDATE customers SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ? WHERE customer_id = ?");
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $address, $customer_id);
    }
    $stmt->execute();
    header("Location: customers.php");
    exit();
}

// Fetch all customer records
$sql = "SELECT * FROM customers ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="stylesheet" href="styles.css">

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
    max-width: 300px;
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

<div class="sidebar">
<div class="logo-container">
<img src="images (1).png" alt="Zoey Food Hub Logo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin: auto;">
            <h2>ZOEY FOOD HUB</h2>
        </div>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="product.php"><i class="fas fa-users"></i> Product</a></li>
            <li><a href="orders.php"><i class="fas fa-chart-line"></i> Orders</a></li>
            <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales</a></li>
            <li><a href="login.php"><i class="fas fa-cogs"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
    <!-- Add Customer Form (Left Side) -->
    <div class="form-container">
        <h2><?= $form_action == "edit_customer" ? "Edit Customer" : "Add New Customer"; ?></h2>
        <form action="customers.php" method="post">
            <?php if ($form_action == "edit_customer"): ?>
                <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customer_id); ?>">
            <?php endif; ?>
            <input type="text" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($first_name); ?>" required>
            <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($last_name); ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email); ?>" required>
            <input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($phone); ?>" required>
            <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($address); ?>" required>
            <button type="submit" name="<?= $form_action; ?>"><?= $form_action == "edit_customer" ? "Update" : "Add"; ?> Customer</button>
        </form>
    </div>

    <!-- Customer List (Right Side) -->
    <div class="table-container">
        <h2>Customer List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['customer_id']); ?></td>
                <td><?= htmlspecialchars($row['first_name']); ?></td>
                <td><?= htmlspecialchars($row['last_name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['phone']); ?></td>
                <td><?= htmlspecialchars($row['address']); ?></td>
                <td class="action-links">
                    <a href="customers.php?edit=<?= $row['customer_id']; ?>">Edit</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>


