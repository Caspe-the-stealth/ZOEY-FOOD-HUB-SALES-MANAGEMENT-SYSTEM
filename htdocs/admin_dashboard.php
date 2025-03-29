<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="product.php"><i class="fas fa-users"></i> Product</a></li>
            <li><a href="orders.php"><i class="fas fa-chart-line"></i> Orders</a></li>
            <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales Records</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="login.php"><i class="fas fa-cogs"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <header>
        
        </header>
        <section class="stats">
            <div class="stats-card">
                <h2>Total Users</h2>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="stats-card">
    <h2>Total Sales</h2>
    <p>₱<?php echo number_format($totalSales, 2); ?></p>
</div>
            <div class="stats-card">
                <h2>Total Orders</h2>
                <p><?php echo $totalOrders; ?></p>
            </div>
        </section>
    </div>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Zoey Food Hub Sales Management System</p>
    </footer>
</body>
</html>