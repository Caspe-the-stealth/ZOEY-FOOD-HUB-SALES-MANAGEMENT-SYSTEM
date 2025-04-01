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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Products</title>
  <link rel="stylesheet" href="product.css"> <!-- External CSS if needed -->
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
        /* Sidebar and general styling */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #E0E0E0;
            margin: 0;
            padding: 0;
            overflow: hidden; // prevent page scroll
        }
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
            margin-left: 270px;
            padding: 15px;  // reduced padding for compact layout
            background: #181818;
            max-height: 100vh; // force main content to fit within viewport height
            overflow-y: auto; // allow internal scrolling if needed
        }
    .main-content button {
      margin-bottom: 20px;
      background: #4DA8DA;
      color: #FFF;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
    }
    .main-content button:hover {
      background: #77D7F9;
    }
    
    /* Table Styling */
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #222;
      border-radius: 8px;
      overflow: hidden;
      margin-bottom: 10px;
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
    /* Modal Styling */
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.8); }
      to { opacity: 1; transform: scale(1); }
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 100;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      width: 40%;
      border: 1px solid #888;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      animation: fadeIn 0.3s ease-in-out;
    }
    /* Updated Modal Header */
    .modal-content h2 {
      background: #000; /* Back it black */
      color: #fff !important; // ensure header text is white
      padding: 15px;
      margin: -20px -20px 20px -20px;
      border-radius: 8px 8px 0 0;
      text-align: center;
    }
    .close {
      color: white;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      margin-top: -5px;
    }
    .close:hover {
      color: #ff5c5c;
    }
    /* Form Styling */
    .form-container {
      background: #242424;
      padding: 20px;
      border-radius: 8px;
      max-width: 600px;
      margin: 20px auto;
      box-shadow: 2px 2px 15px rgba(255, 255, 255, 0.1);
    }
    .form-container form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    .form-container label {
      margin-bottom: 5px;
      color: #F0F0F0;
    }
    .form-container input,
    .form-container textarea,
    .form-container select {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      background: #2D2D2D;
      color: #E0E0E0;
      font-size: 16px;
    }
    .form-container input:focus,
    .form-container textarea:focus,
    .form-container select:focus {
      outline: none;
      box-shadow: 0 0 5px rgba(77, 168, 218, 0.5);
    }
    .form-container button {
      background-color: #4DA8DA;
      color: #FFF;
      border: none;
      padding: 12px 18px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
      width: 100%;
    }
    .form-container button:hover {
      background-color: #77D7F9;
    }
    
    /* Revert edit link styling to plain link styling */
    .action-icons {
        text-decoration: underline;
        background: none;
        padding: 0;
        border: none;
        color: #4DA8DA;
    }
    .action-icons:hover {
        text-decoration: none;
        color: #77D7F9;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
      }
      .main-content {
        margin-left: 220px;
        padding: 15px;
      }
      .modal-content {
        width: 80%;
      }
      .form-container {
        max-width: 100%;
        padding: 15px;
      }
      table, thead th, tbody td {
        font-size: 14px;
        padding: 8px;
      }
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
      <li><a href="product.php"><i class="fas fa-users"></i> Products</a></li>
      <li><a href="orders.php"><i class="fas fa-chart-line"></i> Orders</a></li>
      <li><a href="sales.php"><i class="fas fa-chart-line"></i> Sales </a></li>
      <li><a href="login.php"><i class="fas fa-cogs"></i> Logout</a></li>
    </ul>
  </div>
  <div class="main-content">
    <h1>Product Management</h1>
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
              <a href="edit_product.php?id=<?= $row['Product_ID']; ?>" class="action-icons edit">Edit</a>
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
    <!-- Added form container for eye-catching styling -->
    <div class="form-container">
      <form id="productForm">
        <label>Name:</label>
        <input type="text" id="name" required>
        
        <label>Description:</label>
        <textarea id="description" required></textarea>
        
        <label>Price (₱):</label>
        <input type="number" step="0.01" id="price" required>
        
        <label>Stock Quantity:</label>
        <input type="number" id="stock_quantity" required>
        
        <label>Category:</label>
        <select id="category" required>
          <option value="">Select Category</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat; ?>"><?= $cat; ?></option>
          <?php endforeach; ?>
        </select>
        
        <button type="submit">Add Product</button>
      </form>
    </div>
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
    // Close modal when clicking outside the modal-content
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
        location.reload();
      }).catch(error => console.error('Error:', error));
    };
  </script>
  
  <?php $conn->close(); ?>
</body>
</html>