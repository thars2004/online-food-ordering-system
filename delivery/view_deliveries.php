<?php
include '../includes/db.php';
include 'includes/auth.php';
checkRole('delivery');

$pageTitle = "View Deliveries";
include 'includes/header.php';

// Fetch deliveries that are 'take_overed', 'pick_up', or 'on_the_way', and assigned to the current delivery person
$deliveryPersonId = $_SESSION['user_id'];

// Pagination variables
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number, default is 1
$offset = ($page - 1) * $limit; // Calculate the offset for the query

// Get total count of deliveries
$countQuery = "
    SELECT COUNT(*) as total 
    FROM orders o
    WHERE o.delivery_person_id = $deliveryPersonId 
    AND o.status NOT IN ('pending', 'delivered', 'canceled')
";
$countResult = $conn->query($countQuery);
$totalDeliveries = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalDeliveries / $limit); // Calculate total pages

// Fetch deliveries for the current page
$query = "
    SELECT o.id, o.total_price, o.status, u.name as customer_name, u.address, r.name as restaurant_name 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN restaurants r ON o.restaurant_id = r.id
    WHERE o.delivery_person_id = $deliveryPersonId 
    AND o.status NOT IN ('pending', 'delivered', 'canceled')
    LIMIT $limit OFFSET $offset
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Deliveries - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>My Deliveries</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Restaurant</th>
                    <th>Customer Name</th>
                    <th>Customer Address</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo $order['restaurant_name']; ?></td>
                    <td><?php echo $order['customer_name']; ?></td>
                    <td><?php echo $order['address']; ?></td>
                    <td><?php echo $order['total_price']; ?></td>
                    <td><?php echo $order['status']; ?></td>
                    <td>
                        <?php if ($order['status'] == 'take_overed'): ?>
                            <form method="POST" action="pickup_order.php">
                                <input type="hidden" name="order_id" value='<?php echo $order['id']; ?>'>
                                <button type="submit" name="pickup" class="btn btn-warning">Mark as Picked Up</button>
                            </form>
                        <?php elseif ($order['status'] == 'pick_up'): ?>
                            <form method="POST" action="on_the_way.php">
                                <input type="hidden" name="order_id" value='<?php echo $order['id']; ?>'>
                                <button type="submit" name="on_the_way" class="btn btn-info">Mark as On the Way</button>
                            </form>
                        <?php elseif ($order['status'] == 'on_the_way'): ?>
                            <form method="POST" action="delivered_order.php">
                                <input type="hidden" name="order_id" value='<?php echo $order['id']; ?>'>
                                <button type="submit" name="delivered" class="btn btn-success">Mark as Delivered</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <nav>
    <ul class="pagination">
        <!-- Previous Page Link -->
        <li class="page-item <?php if($page <= 1) { echo 'disabled'; } ?>">
            <a class="page-link" href="<?php if($page > 1){ echo '?page=' . ($page - 1); } else { echo '#'; } ?>">Previous</a>
        </li>

        <!-- Numbered Page Links -->
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?php if($i == $page) { echo 'active'; } ?>">
            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
        <?php endfor; ?>

        <!-- Next Page Link -->
        <li class="page-item <?php if($page >= $totalPages) { echo 'disabled'; } ?>">
            <a class="page-link" href="<?php if($page < $totalPages){ echo '?page=' . ($page + 1); } else { echo '#'; } ?>">Next</a>
        </li>
    </ul>
</nav>

    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
