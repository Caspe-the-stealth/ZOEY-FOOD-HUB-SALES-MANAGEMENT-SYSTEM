<?php
include 'db_connection.php'; // Database connection

// Fetch products from the database
$query = "SELECT * FROM products";
$result = $conn->query($query);

// Fetch categories for dropdown
$categoryResult = $conn->query("SELECT DISTINCT Category FROM products");
$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['Category'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="product.css"> <!-- External CSS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        .close {
            color: red;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
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
            <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales Records</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="login.php"><i class="fas fa-cogs"></i> Logout</a></li>
        </ul>
    </div>
<div class="main-content">
    <h2>Product Management</h2>
    <button id="openModal">➕ Add New Product</button>

    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price (₱)</th>
                <th>Stock</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['Product_ID']; ?></td>
                <td><?= $row['Name']; ?></td>
                <td><?= $row['Description']; ?></td>
                <td><?= number_format($row['Price'], 2); ?></td>
                <td><?= $row['Stock_Quantity']; ?></td>
                <td><?= $row['Category']; ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $row['Product_ID']; ?>" class="action-icons edit">✏️</a>
    
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add New Product</h2>
        <form id="productForm">
            <label>Name:</label>
            <input type="text" id="name" required><br>

            <label>Description:</label>
            <textarea id="description" required></textarea><br>

            <label>Price (₱):</label>
            <input type="number" step="0.01" id="price" required><br>

            <label>Stock Quantity:</label>
            <input type="number" id="stock_quantity" required><br>

            <label>Category:</label>
            <select id="category" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat; ?>"><?= $cat; ?></option>
                <?php endforeach; ?>
            </select><br>


            <button type="submit">Add Product</button>
        </form>
    </div>
</div>

<script>
    // Open Modal
    document.getElementById('openModal').onclick = function() {
        document.getElementById('addProductModal').style.display = 'block';
    };

    // Close Modal
    document.querySelector('.close').onclick = function() {
        document.getElementById('addProductModal').style.display = 'none';
    };

    // Close modal when clicking outside the content
    window.onclick = function(event) {
        if (event.target == document.getElementById('addProductModal')) {
            document.getElementById('addProductModal').style.display = 'none';
        }
    };

    // Handle Form Submission
    document.getElementById('productForm').onsubmit = function(event) {
        event.preventDefault();
        
        var formData = new FormData();
        formData.append('name', document.getElementById('name').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('price', document.getElementById('price').value);
        formData.append('stock_quantity', document.getElementById('stock_quantity').value);
        formData.append('category', document.getElementById('category').value);

        fetch('add_product_backend.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text()).then(data => {
            alert(data);
            location.reload(); // Refresh the page
        }).catch(error => console.error('Error:', error));
    };
</script>

<?php $conn->close(); ?>
</body>
</html>
