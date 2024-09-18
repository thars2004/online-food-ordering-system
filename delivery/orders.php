<?php
include '../includes/db.php';
include 'includes/auth.php';
checkRole('delivery');
$pageTitle = "Orders";
include 'includes/header.php';


$recordsPerPage = 10; // Number of records to display per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Fetch all pending orders for this delivery person
$query = "
    SELECT o.id, o.total_price, o.status, u.name as customer_name, u.address, r.name as restaurant_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN restaurants r ON o.restaurant_id = r.id
    WHERE o.status = 'pending'
    LIMIT $recordsPerPage OFFSET $offset
";


$totalOrdersQuery = "SELECT COUNT(*) as total FROM orders WHERE status = 'pending'";
$totalOrdersResult = $conn->query($totalOrdersQuery);
$totalOrders = $totalOrdersResult->fetch_assoc()['total'];
$totalPages = ceil($totalOrders / $recordsPerPage);

$result = $conn->query($query);

// Function to handle status change when 'Take Over' button is clicked
if (isset($_POST['take_over'])) {
    $orderId = $_POST['order_id'];
    $deliveryPersonId = $_SESSION['user_id']; // Assuming the delivery person's ID is stored in the session

    // Update order status to 'take_overed' and set the delivery person ID
    $updateOrderQuery = "UPDATE orders SET status = 'take_overed', delivery_person_id = ? WHERE id = ?";
    
    // Prepare the statement to avoid SQL injection
    if ($stmtUpdateOrder = $conn->prepare($updateOrderQuery)) {
        $stmtUpdateOrder->bind_param("ii", $deliveryPersonId, $orderId);
        
        if ($stmtUpdateOrder->execute()) {
            // Check if the delivery record already exists
            $checkDeliveryQuery = "SELECT id FROM delivery WHERE order_id = ?";
            
            if ($stmtCheckDelivery = $conn->prepare($checkDeliveryQuery)) {
                $stmtCheckDelivery->bind_param("i", $orderId);
                $stmtCheckDelivery->execute();
                $stmtCheckDelivery->store_result();
                
                if ($stmtCheckDelivery->num_rows > 0) {
                    // Update existing delivery record
                    $updateDeliveryQuery = "UPDATE delivery SET delivery_person_id = ?, status = 'assigned' WHERE order_id = ?";
                    
                    if ($stmtUpdateDelivery = $conn->prepare($updateDeliveryQuery)) {
                        $stmtUpdateDelivery->bind_param("ii", $deliveryPersonId, $orderId);
                        
                        if ($stmtUpdateDelivery->execute()) {
                            echo "<script>alert('Order status updated to Take Overed and delivery record set to Assigned'); window.location.href='orders.php';</script>";
                        } else {
                            echo "<script>alert('Failed to update delivery record');</script>";
                        }
                    } else {
                        echo "<script>alert('Failed to prepare update delivery statement');</script>";
                    }
                } else {
                    // Insert new delivery record
                    $insertDeliveryQuery = "INSERT INTO delivery (order_id, delivery_person_id, status) VALUES (?, ?, 'assigned')";
                    
                    if ($stmtInsertDelivery = $conn->prepare($insertDeliveryQuery)) {
                        $stmtInsertDelivery->bind_param("ii", $orderId, $deliveryPersonId);
                        
                        if ($stmtInsertDelivery->execute()) {
                            echo "<script>alert('Order status updated to Take Overed and new delivery record set to Assigned'); window.location.href='orders.php';</script>";
                        } else {
                            echo "<script>alert('Failed to insert delivery record');</script>";
                        }
                    } else {
                        echo "<script>alert('Failed to prepare insert delivery statement');</script>";
                    }
                }
                
                $stmtCheckDelivery->close();
            } else {
                echo "<script>alert('Failed to prepare check delivery statement');</script>";
            }
        } else {
            echo "<script>alert('Failed to update order status');</script>";
        }
        
        $stmtUpdateOrder->close();
    } else {
        echo "<script>alert('Failed to prepare update order statement');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>All Pending Orders</h2>
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
                        <?php if ($order['status'] == 'pending'): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value='<?php echo $order['id']; ?>'>
                            <button type="submit" name="take_over" class="btn btn-success">Take Over</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
