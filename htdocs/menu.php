
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Zoey Food Hub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Zoey Food Hub - Menu</h1>
        <nav>
            <a href="customer_dashboard.php">Home</a> |
            <a href="cart.php">Cart</a> |
            <a href="login.php">Logout</a>
        </nav>
    </header>
    <div class="menu-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="food-item">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
                    <button onclick="orderFood(<?php echo $row['id']; ?>)">Order Now</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No food items available.</p>
        <?php endif; ?>
    </div>
    <script>
        function orderFood(id) {
            window.location.href = "order.php?food_id=" + id;
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>