<?php
include '../includes/db.php'; // Database connection
include 'includes/header.php';
include '../includes/auth.php';
checkRole('customer');


// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's order history
$stmt = $conn->prepare("SELECT o.id as order_id, o.total_price, o.created_at, r.name as restaurant_name 
                        FROM orders o 
                        JOIN restaurants r ON o.restaurant_id = r.id 
                        WHERE o.user_id = ? 
                        ORDER BY o.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orderResult = $stmt->get_result();
$orders = $orderResult->fetch_all(MYSQLI_ASSOC);

// Fetch order items for each order
function getOrderItems($conn, $order_id) {
    $stmt_items = $conn->prepare("SELECT oi.quantity, oi.price, fi.name 
                                  FROM order_items oi 
                                  JOIN food_items fi ON oi.food_item_id = fi.id 
                                  WHERE oi.order_id = ?");
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    return $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .order-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }
        .order-item h5 {
            margin-bottom: 10px;
        }
        .order-item ul {
            list-style: none;
            padding: 0;
        }
        .order-item ul li {
            padding: 5px 0;
        }
        .order-item hr {
            margin: 10px 0;
        }
        .order-item p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Your Order History</h2>
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">
                <p>You have no past orders.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $index => $order): ?>
                <div class="order-item">
                    <h5>Order #<?php echo $order['order_id'] ?> - <?php echo date("F j, Y, g:i a", strtotime($order['created_at'])); ?></h5>
                    <p><strong>Restaurant:</strong> <?php echo htmlspecialchars($order['restaurant_name']); ?></p>
                    <p><strong>Total Price:</strong> ₹<?php echo number_format($order['total_price'], 2); ?></p>

                    <h6>Items Ordered:</h6>
                    <ul>
                        <?php
                        $orderItems = getOrderItems($conn, $order['order_id']);
                        foreach ($orderItems as $item):
                        ?>
                            <li><?php echo htmlspecialchars($item['name']); ?> - Quantity: <?php echo $item['quantity']; ?> - ₹<?php echo number_format($item['price'], 2); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
