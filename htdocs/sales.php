
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" style="">
</head>




<body class="container mt-4">

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
            <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales Records</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="login.php"><i class="fas fa-cogs"></i> Logout</a></li>
        </ul>
    </div>

    <h2 class="mb-3 text-center">Sales Records</h2>

    <!-- Total Revenue -->
    <div class="alert alert-success text-center">
        <h4>Total Sales Revenue: $<?php echo number_format($total_revenue, 2); ?></h4>
    </div>

    <!-- Sales Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price ($)</th>
                    <th>Total Amount ($)</th>
                    <th>Order Status</th>
                    <th>Payment Status</th>
                    <th>Order Date</th>
                    <th>Delivery Address</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $sales_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['Order_ID']; ?></td>
                        <td><?php echo $row['Full_Name']; ?></td>
                        <td><?php echo $row['Product_Name']; ?></td>
                        <td><?php echo $row['Category']; ?></td>
                        <td><?php echo number_format($row['Price'], 2); ?></td>
                        <td><?php echo number_format($row['Total_Amount'], 2); ?></td>
                        <td><?php echo $row['Order_Status']; ?></td>
                        <td class="<?php echo ($row['Payment_Status'] == 'Paid') ? 'text-success' : 'text-danger'; ?>">
                            <?php echo $row['Payment_Status']; ?>
                        </td>
                        <td><?php echo date("d-M-Y H:i A", strtotime($row['Order_Date'])); ?></td>
                        <td><?php echo $row['Delivery_Address']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>


    
</body>
</html>
