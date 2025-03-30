<?php
session_start();
include 'db_connection.php';

// Fetch categories for filtering
$category_query = "SELECT DISTINCT category FROM products";
$category_result = $conn->query($category_query);

// Fetch menu items based on selected category
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$sql = "SELECT * FROM products";
if (!empty($category_filter)) {
    $sql .= " WHERE category = ?";
}

$stmt = $conn->prepare($sql);
if (!empty($category_filter)) {
    $stmt->bind_param("s", $category_filter);
}
$stmt->execute();
$result = $stmt->get_result();

$_SESSION['name'] = $customer_name; // Assuming $customer_name holds the user's name from the database
?>
<?php 
$cart_count = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0; 
?>
<a href="cart.php" class="cart-link">
    Cart
    <?php if ($cart_count > 0): ?>
        <span class="cart-badge"><?= $cart_count; ?></span>
    <?php endif; ?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | ZoeY Food Hub</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .menu-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            text-align: center;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }
        .menu-container h2 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }
        .message {
            color: green;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .category-filter {
            margin-bottom: 20px;
        }
        .category-filter select {
            padding: 8px;
            font-size: 16px;
            border-radius: 5px;
        }
        .menu-items {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .menu-item {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            width: 260px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .menu-item:hover {
            transform: scale(1.05);
        }
        .menu-item img {
            max-width: 100%;
            border-radius: 8px;
            height: 160px;
            object-fit: cover;
        }
        .menu-item h3 {
            font-size: 20px;
            color: #333;
            margin: 10px 0;
        }
        .menu-item p {
            font-size: 16px;
            color: #555;
        }
        .stock {
            font-size: 14px;
            color: #777;
            margin-bottom: 5px;
        }
        .menu-item form {
            margin-top: 10px;
        }
        .menu-item input[type="number"] {
            width: 50px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .menu-item button {
            padding: 8px 12px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .menu-item button:hover {
            background: #333;
        }
        .menu-item button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
          /* Navbar Styles */
          .navbar {
    background-color: #333; /* Dark background */
    display: flex;
    justify-content: space-between; /* Spread items */
    align-items: center;
    padding: 10px 15px;
    position: fixed;
    top: 0;
    left: 0;
    width: 98%;
    height: 48px; /* Adjust height */
    z-index: 1000;
}
        .navbar > div {
            display: flex;
            justify-content: center; /* Centers the links */
            flex: 1; /* Makes the div take full width */
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            margin: 0 15px; /* Adds space between links */
            transition: color 0.3s ease;
        }
        .navbar a:hover {
            color: #f39c12;
        }
        /* Profile Section */
        .profile-section {
            display: flex;
            align-items: center;
        }
        .profile-section img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid white;
        }
        .logout-btn {
            background: red;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }
        .logout-btn:hover {
            background: darkred;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div>
        <a href="home.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="cart.php">Cart</a>
    </div>



    <div class="profile-section">
        <!-- Cart Icon Section -->
<div class="cart-section">
    <a href="cart.php" class="cart-link">
        <i class="fa fa-shopping-cart"></i>
        <?php if ($cart_count > 0): ?>
            <span class="cart-badge"><?= $cart_count; ?></span>
        <?php endif; ?>
    </a>
</div>

        <img src="https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcSiySllOb5l9TCy1S9s_1klLXR6ka72DWGBzMXEi_HPw0E3h_29Dp7lfFhGYqNSPTQFkmpt_WCjidu0WLwlU5TWqQ" alt="Profile">
        <?php if (isset($_SESSION['name']) && !empty($_SESSION['name'])): ?>
            <a href="profile.php">Welcome, <?= htmlspecialchars($_SESSION['name']); ?></a>
        <?php else: ?>
            <a href="login.php">LogOut</a>
        <?php endif; ?>
    </div>
</div>

<div class="menu-container">
    <h2>Our Food Menu</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <p class="message"><?= $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <!-- Category Filter -->
    <div class="category-filter">
        <form method="GET">
            <select name="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php while ($cat = $category_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($cat['category']); ?>" 
                        <?= ($category_filter == $cat['category']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($cat['category']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>




        
    </div>

    <div class="menu-items">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="menu-item">
                <img src="<?= htmlspecialchars($row['Image_URL']); ?>" alt="<?= htmlspecialchars($row['Name']); ?>">
                <h3><?= htmlspecialchars($row['Name']); ?></h3>
                <p><?= htmlspecialchars($row['Description']); ?></p>
                <p><strong>₱<?= number_format($row['Price'], 2); ?></strong></p>
                <p class="stock">Stock: <?= $row['Stock_Quantity']; ?> available</p>
                <form method="POST" action="cart.php">
    <input type="hidden" name="product_id" value="<?= $row['Product_ID']; ?>">
    <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['Name']); ?>">
    <input type="hidden" name="price" value="<?= $row['Price']; ?>">
    <input type="number" name="quantity" value="1" min="1" max="<?= $row['Stock_Quantity']; ?>" required>
    <button type="submit" name="add_to_cart" <?= ($row['Stock_Quantity'] == 0) ? 'disabled' : ''; ?>>
        <?= ($row['Stock_Quantity'] == 0) ? 'Out of Stock' : 'Buy Food'; ?>
    </button>
</form>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>