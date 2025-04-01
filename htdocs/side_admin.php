<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ZOEY FOOD HUB SALES MANAGEMENT SYSTEM</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Optional: Include Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
<!-- Fixed the sidebar container tag and removed inline styles -->
<div class="sidebar">
    <div class="logo-container">
        <img src="images (1).png" alt="Zoey Food Hub Logo" class="logo">
        <h2>ZOEY FOOD HUB</h2>
    </div>
    <ul>
        <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
        <li><a href="product.php"><i class="fas fa-box"></i> Products</a></li>
        <li><a href="orders.php"><i class="fas fa-chart-line"></i> Orders</a></li>
        <li><a href="sales.php"><i class="fas fa-chart-pie"></i> Sales</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
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
    max-width: auto;
}

.table-container {
    flex-grow: 1; /* Allow table to take remaining space */
}

  /* Sidebar and general styling */
  body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #E0E0E0;
            margin: 0;
            padding: 0;
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

</body>
</html>