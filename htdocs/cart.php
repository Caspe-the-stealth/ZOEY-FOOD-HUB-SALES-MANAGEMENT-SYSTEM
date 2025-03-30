<?php
session_start();
include 'db_connection.php';

// Initialize the cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If the "Add to Cart" button is clicked, process the order:
// Transfer the cart to a temporary sales_order, insert each item into the order_list table,
// clear the cart, and set a success notification.
if (isset($_GET['order_to_sales'])) {
    // Save the current cart into sales_order session variable.
    $_SESSION['sales_order'] = $_SESSION['cart'];
    $_SESSION['cart'] = [];
    
    // Insert each sales_order item into the order_list table
    if (isset($_SESSION['sales_order']) && !empty($_SESSION['sales_order']) && isset($_SESSION['customer_id'])) {
        $customer_id = $_SESSION['customer_id'];
        $date = date('Y-m-d H:i:s');
        foreach ($_SESSION['sales_order'] as $product_id => $item) {
            $order_quantity = $item['quantity'];
            $price = $item['price'];
            $total_payment = $price * $order_quantity;
            
            // Prepare and execute the insert statement for each product
            $stmt = $conn->prepare("INSERT INTO order_list (product_id, customer_id, order_quantity, price, total_payment, date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiidss", $product_id, $customer_id, $order_quantity, $price, $total_payment, $date);
            $stmt->execute();
            $stmt->close();
        }
        // Clear sales_order after insertion
        $_SESSION['sales_order'] = [];
        $_SESSION['success'] = "Successfully added to cart!";
    }
}

// Process add-to-cart if coming from a products page (menu.php)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    // Use keys as passed by menu.php: product_id, product_name, price, quantity
    $product_id   = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price        = floatval($_POST['price']);
    $quantity     = intval($_POST['quantity']);

    if ($quantity > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name'     => $product_name,
                'price'    => $price,
                'quantity' => $quantity
            ];
        }
    }
}

// Remove an item from cart
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

// -----------------
// Order History Section
// -----------------
$order_history = [];
if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $query = "SELECT order_id, product_id, order_quantity, price, total_payment, date 
              FROM order_list 
              WHERE customer_id = ? 
              ORDER BY date DESC";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()){
            $order_history[] = $row;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart & Order History | ZoeY Food Hub</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .cart-container, .history-container {
            max-width: 800px;
            margin: 70px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 12px; text-align: center; }
        .actions a {
            color: #ff4757;
            text-decoration: none;
            font-weight: bold;
        }
        .actions a:hover { text-decoration: underline; }
        .btn {
            background: #000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin: 10px 5px 0 0;
            cursor: pointer;
        }
        .btn:hover { background: #333; }
        .cart-links, .history-links {
            margin-top: 15px;
            text-align: center;
        }
        /* Navbar Styles */
        .navbar {
            background-color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            position: fixed;
            top: 0;
            left: 0;
            width: 98%;
            height: 48px;
            z-index: 1000;
        }
        .nav-left, .nav-right {
            display: flex;
            align-items: center;
        }
        .nav-left a, .nav-right a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            margin: 0 15px;
            transition: color 0.3s ease;
        }
        .nav-left a:hover, .nav-right a:hover { color: #f39c12; }
        .profile-section {
            display: flex;
            align-items: center;
        }
        .profile-section img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid #fff;
        }
    </style>
    <script>
        // Function to update line total and overall cart total when quantity changes
        function updateTotals() {
            let overallTotal = 0;
            document.querySelectorAll('.quantity-input').forEach(function(input) {
                const quantity = parseInt(input.value);
                const price = parseFloat(input.closest('tr').querySelector('.price').dataset.price);
                const lineTotal = quantity * price;
                input.closest('tr').querySelector('.line-total').innerText = "₱" + lineTotal.toFixed(2);
                overallTotal += lineTotal;
            });
            document.getElementById('overall-total').innerText = "₱" + overallTotal.toFixed(2);
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.quantity-input').forEach(function(input) {
                input.addEventListener('input', updateTotals);
            });
        });
    </script>
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
    <h2>Your Shopping Cart</h2>
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
                            $item_total = $item['price'] * $item['quantity'];
                            $total_price += $item_total;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']); ?></td>
                        <td class="price" data-price="<?= $item['price']; ?>">
                            <?= "₱" . number_format($item['price'], 2); ?>
                        </td>
                        <td>
                            <input type="number" class="quantity-input" name="quantities[<?= $product_id; ?>]" 
                                   value="<?= $item['quantity']; ?>" min="1">
                        </td>
                        <td class="line-total"><?= "₱" . number_format($item_total, 2); ?></td>
                        <td class="actions">
                           <a href="cart.php?remove=<?= $product_id; ?>" 
                              onclick="return confirm('Remove this item?');">Remove</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Total Price: <span id="overall-total"><?= "₱" . number_format($total_price, 2); ?></span></h3>
            <button type="submit" name="update_cart" class="btn">Update Cart</button>
            <a href="cart.php?clear_cart=1" class="btn" onclick="return confirm('Clear cart?');">Clear Cart</a>
        </form>
    <?php else: ?>
        <p>Your shopping cart is empty.</p>
    <?php endif; ?>
    <div class="cart-links">
        <a href="menu.php" class="btn">Continue Shopping</a>
        <!-- When clicked, the current cart is processed and added to order history -->
        <a href="cart.php?order_to_sales=1" class="btn">Add to Cart</a>
    </div>
</div>

<!-- Order History Section -->
<div class="history-container">
    <h2>Order History</h2>
    <?php if (!empty($order_history)): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product ID</th>
                    <th>Quantity</th>
                    <th>Price (₱)</th>
                    <th>Total (₱)</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_history as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_id']); ?></td>
                    <td><?= htmlspecialchars($order['product_id']); ?></td>
                    <td><?= htmlspecialchars($order['order_quantity']); ?></td>
                    <td><?= "₱" . number_format($order['price'], 2); ?></td>
                    <td><?= "₱" . number_format($order['total_payment'], 2); ?></td>
                    <td><?= htmlspecialchars($order['date']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have not purchased anything yet.</p>
    <?php endif; ?>
    <div class="history-links">
        <a href="menu.php" class="btn">Shop More</a>
    </div>
</div>

</body>
</html>