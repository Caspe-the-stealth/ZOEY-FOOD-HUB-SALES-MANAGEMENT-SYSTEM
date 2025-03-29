<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

// Handle DELETE user request
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: users.php");
    exit();
}

// Fetch users
$query = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($query);
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="users.css"> <!-- Link to external CSS -->
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
            <li><a href="#"><i class="fas fa-chart-line"></i> Sales</a></li>
            <li><a href="#"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="login.php"><i class="fas fa-cogs"></i> Logout</a></li>
        </ul>
    </div>

<div class="main-content">
    <h2>Manage Users</h2>

    <a href="add_user.php" class="btn">+ Add User</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= isset($row['id']) ? $row['id'] : 'N/A'; ?></td>
                <td><?= isset($row['Name']) ? $row['Name'] : 'N/A'; ?></td>
                <td><?= isset($row['Email']) ? $row['Email'] : 'N/A'; ?></td>
                <td><?= isset($row['Role']) ? $row['Role'] : 'N/A'; ?></td>
                <td><?= isset($row['Phone']) ? $row['Phone'] : 'N/A'; ?></td>
                <td><?= isset($row['Address']) ? $row['Address'] : 'N/A'; ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $row['id']; ?>" class="edit-btn">Edit</a>
                    <a href="users.php?delete=<?= $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
