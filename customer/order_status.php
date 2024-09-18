<?php
include '../includes/db.php';
include '../includes/auth.php';
include 'includes/header.php';
checkRole('customer'); // Only allow customer users

$customer_id = $_SESSION['user_id']; // Assuming the customer ID is stored in session

// Pagination settings
$limit = 10; // Number of orders per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get the current page number
$offset = ($page - 1) * $limit; // Calculate the offset for the SQL query

// Fetch customer orders from the database with pagination
$query = "
    SELECT o.id, o.status, o.created_at
    FROM orders o
    WHERE o.user_id = $customer_id
    ORDER BY o.created_at DESC
    LIMIT $limit OFFSET $offset
";
$result = $conn->query($query);

// Fetch total number of orders to calculate total pages
$totalQuery = "
    SELECT COUNT(*) AS total
    FROM orders
    WHERE user_id = $customer_id
";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalOrders = $totalRow['total'];
$totalPages = ceil($totalOrders / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status</title>
    <!-- Include Bootstrap CSS and FontAwesome CSS -->
    <style>
        @import url('https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
    </style>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3 class="mb-4">Your Order Status</h3>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td>
                                <?php echo ucfirst($row['status']); ?>
                                <!-- Optional: Add a color code for each status -->
                                <?php if ($row['status'] == 'delivered'): ?>
                                    <span class="badge badge-success">Delivered</span>
                                <?php elseif ($row['status'] == 'canceled'): ?>
                                    <span class="badge badge-danger">Canceled</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">In Progress</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <!-- Display Cancel button only if the order is pending -->
                                <?php if ($row['status'] == 'pending'): ?>
                                    <form action="cancel_order.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">Not cancellable</span> <!-- Optionally display a message for other statuses -->
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <!-- Pagination controls -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo max($page - 1, 1); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo min($page + 1, $totalPages); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
