<?php
session_start();

// Initialize the cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart (if coming from a products page)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
        // If product already in cart, update quantity
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Add new product to cart
            $_SESSION['cart'][$product_id] = [
                'name' => $product_name,
                'price' => $price,
                'quantity' => $quantity
            ];
        }
    }
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
}

// Update cart quantities
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $_SESSION['cart'][$product_id]['quantity'] = max(1, intval($quantity));
    }
}

// Clear the cart
if (isset($_GET['clear_cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | ZoeY Food Hub</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .cart-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .actions a {
            color: #ff4757;
            text-decoration: none;
            font-weight: bold;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .btn {
            background: #000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 10px;
            cursor: pointer;
        }
        .btn:hover {
            background: #333;
        }
        .cart-links {
            margin-top: 15px;
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
        .nav-left,
        .nav-right {
            display: flex;
            align-items: center;
        }
        .nav-left a,
        .nav-right a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            margin: 0 15px;
            transition: color 0.3s ease;
        }
        .nav-left a:hover,
        .nav-right a:hover {
            color: #f39c12;
        }
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
    <div class="nav-left">
        <a href="menu.php">Menu</a>
        <a href="cart.php">Cart</a>
    </div>
    <div class="nav-right profile-section">
        <img src="https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcSiySllOb5l9TCy1S9s_1klLXR6ka72DWGBzMXEi_HPw0E3h_29Dp7lfFhGYqNSPTQFkmpt_WCjidu0WLwlU5TWqQ" alt="Profile">
        <?php if (isset($_SESSION['name']) && !empty($_SESSION['name'])): ?>
            <a href="profile.php">Welcome, <?= htmlspecialchars($_SESSION['name']); ?></a>
        <?php else: ?>
            <a href="login.php">LogOut</a>
        <?php endif; ?>
    </div>
    
</div>

<div class="cart-container">
    <br>
    <br>

    <br>

    <br>

    <br>
    <h2>Shopping Cart</h2>
    <?php if (!empty($_SESSION['cart'])): ?>
        <form method="POST" action="cart.php">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price (₱)</th>
                        <th>Quantity</th>
                        <th>Total (₱)</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_price = 0;
                    foreach ($_SESSION['cart'] as $product_id => $item): 
                        $total = $item['price'] * $item['quantity'];
                        $total_price += $total;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']); ?></td>
                        <td><?= "₱" . number_format($item['price'], 2); ?></td>
                        <td>
                            <input type="number" name="quantities[<?= $product_id; ?>]" 
                                   value="<?= $item['quantity']; ?>" min="1">
                        </td>
                        <td><?= "₱" . number_format($total, 2); ?></td>
                        <td class="actions">
                           <a href="cart.php?remove=<?= $product_id; ?>" 
                              onclick="return confirm('Are you sure you want to remove this item?');">
                              Remove
                           </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Total Price: <?= "₱" . number_format($total_price, 2); ?></h3>
            <button type="submit" name="update_cart" class="btn">Update Cart</button>
            <a href="cart.php?clear_cart=1" class="btn" 
               onclick="return confirm('Are you sure you want to clear the cart?');">Clear Cart</a>
        </form>
    <?php else: ?>
        <p>Your shopping cart is empty.</p>
    <?php endif; ?>
    <div class="cart-links">
        <a href="menu.php" class="btn">Continue Shopping</a>
        <a href="checkout.php" class="btn">Proceed to Checkout</a>
    </div>
</div>
</body>
</html>