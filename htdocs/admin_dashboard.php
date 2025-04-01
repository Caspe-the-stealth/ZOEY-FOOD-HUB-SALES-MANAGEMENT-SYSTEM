<?php
session_start();
include 'db_connection.php';

// Fetch total customers count
$queryTotalCustomers = "SELECT COUNT(*) AS total FROM customers";
$resultTotalCustomers = $conn->query($queryTotalCustomers);
$totalUsers = 0;
if ($resultTotalCustomers) {
    $row = $resultTotalCustomers->fetch_assoc();
    $totalUsers = $row['total'];
}

// Fetch total orders count
$queryTotalOrders = "SELECT COUNT(*) AS total FROM orders";
$resultTotalOrders = $conn->query($queryTotalOrders);
$totalOrders = 0;
if ($resultTotalOrders) {
    $row = $resultTotalOrders->fetch_assoc();
    $totalOrders = $row['total'];
}

// Fetch total sales from completed orders
$queryTotalSales = "SELECT SUM(Total_Amount) AS total_sales FROM orders WHERE Order_Status = 'Completed'";
$resultTotalSales = $conn->query($queryTotalSales);
$totalSales = 0;
if ($resultTotalSales) {
    $row = $resultTotalSales->fetch_assoc();
    $totalSales = $row['total_sales'];
}

// Fetch best sales product (aggregated by total sales)
$queryBestProduct = "SELECT p.Name AS product_name, SUM(o.Total_Amount) AS total_product_sales 
                      FROM orders o 
                      JOIN products p ON o.Product_ID = p.Product_ID 
                      WHERE o.Order_Status = 'Completed'
                      GROUP BY p.Product_ID
                      ORDER BY total_product_sales DESC LIMIT 1";
$resultBestProduct = $conn->query($queryBestProduct);
$bestProduct = "None";
if ($resultBestProduct && $row = $resultBestProduct->fetch_assoc()) {
    $bestProduct = $row['product_name'] . " (₱" . number_format($row['total_product_sales'], 2) . ")";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Sidebar and general styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #E0E0E0;
            margin: 0;
            padding: 0;
            /* Prevent scrolling */
            overflow: hidden;
            
        }
        .sidebar {
            width: 250px;
            background: #1E1E1E;
            height: auto;
            min-height: 100vh;
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
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: auto;
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
            margin-left: 240px;
            padding: 35px;
            background: #181818;
            max-height: 100vh;
            overflow-y: auto;
            min-height: 100vh;
            gap: 20px;
        }
        .stats {
            
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 35px;
        }
        .stats-card {
            
            background: #242424;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 2px 2px 15px rgba(255, 255, 255, 0.1);
            flex: 2;
            min-width: 200px;
            text-align: center;
            font-size: 16px;
            
        }
        .stats-card h2 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #F0F0F0;
        }
        .stats-card p {
            font-size: 20px;
            margin: 0;
        }
        .chart-section {
            background: #242424;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 2px 2px 15px rgba(255, 255, 255, 0.1);
            max-width: 700px;
            margin: auto;
        }
        .chart-section h2 {
            text-align: center;
            color: #F0F0F0;
            margin-bottom: 20px;
        }
        .notification {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
        <img src="images (1).png" alt="Zoey Food Hub Logo" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; display: block; margin: auto;">
           <br>
        <h2>ZOEY FOOD HUB</h2>
        </div>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="product.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-chart-line"></i> Orders</a></li>
            <li><a href="sales.php"><i class="fas fa-chart-pie"></i> Sales</a></li>
            <li><a href="index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>

    </div>
    
    <div class="main-content">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="notification"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <section class="stats">
            
            <div class="stats-card">
                <h2>Total Sales</h2>
                <p>₱<?php echo number_format($totalSales, 2); ?></p>
            </div>
            <div class="stats-card">
                <h2>Total Orders</h2>
                <p><?php echo $totalOrders; ?></p>
            </div>
            <div class="stats-card">
                <h2>Total Customers</h2>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="stats-card">
                <h2>Best Sales Product</h2>
                <p><?php echo $bestProduct; ?></p>
            </div>
        </section>
        <section class="chart-section">
            <h2>Sales Flowchart</h2>
            <canvas id="salesChart"></canvas>
        </section>
    </div>
    <br>
    <footer style="text-align: center; padding: 20px; background: #1E1E1E; color: #CCCCCC;">
        <p>&copy; <?php echo date("Y"); ?> Zoey Food Hub Sales Management System</p>
    </footer>
    <script>
        // Sample data - replace these arrays with your actual data
        var salesData = {
            daily: 1500,
            weekly: 10500,
            monthly: 42000,
            yearly: 500000
        };

        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Daily', 'Weekly', 'Monthly', 'Yearly'],
                datasets: [{
                    label: 'Sales',
                    data: [salesData.daily, salesData.weekly, salesData.monthly, salesData.yearly],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: "#F0F0F0"
                        }
                    },
                    x: {
                        ticks: {
                            color: "#F0F0F0"
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: "#F0F0F0"
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>