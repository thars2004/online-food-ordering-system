<?php
include '../includes/db.php'; // Database connection
include 'includes/header.php';
include '../includes/auth.php';
checkRole('customer');


// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = false;
$user_id = $_SESSION['user_id'];

// Retrieve order information
if (!isset($_SESSION['order_id'])) {
    $errors[] = "No order found. Please place an order first.";
} else {
    $order_id = $_SESSION['order_id'];

    // Fetch order details
    $orderQuery = "SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id";
    $orderResult = mysqli_query($conn, $orderQuery);
    $order = mysqli_fetch_assoc($orderResult);

    if (!$order) {
        $errors[] = "Invalid order.";
    }

    // Fetch ordered items
    $itemsQuery = "SELECT oi.*, fi.name, fi.price FROM order_items oi 
                   JOIN food_items fi ON oi.food_item_id = fi.id 
                   WHERE oi.order_id = $order_id";
    $itemsResult = mysqli_query($conn, $itemsQuery);
    $items = mysqli_fetch_all($itemsResult, MYSQLI_ASSOC);
}

// Handle payment and order confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];

    // Insert into transactions table
    $stmt = $conn->prepare("INSERT INTO transactions (order_id, user_id, amount, payment_method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $order_id, $user_id, $order['total_price'], $payment_method);

    if ($stmt->execute()) {
        $success = true;

        // Mark the order as completed
        $updateOrder = "UPDATE orders SET status = 'completed' WHERE id = $order_id";
        mysqli_query($conn, $updateOrder);

        // Clear session data related to the order
        unset($_SESSION['order_id']);
    } else {
        $errors[] = "Failed to process the payment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Checkout</h2>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <h4 class="alert-heading">Payment Successful!</h4>
                <p>Your payment has been successfully processed. Thank you for your order!</p>
                <a href="index.php" class="btn btn-primary">Return to Home</a>
            </div>
        <?php elseif (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h4 class="alert-heading">Error</h4>
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
                <a href="order_food.php" class="btn btn-primary">Return to Order</a>
            </div>
        <?php else: ?>
            <div class="order-summary">
                <h4>Order Summary</h4>
                <ul class="list-group mb-4">
                    <?php foreach ($items as $item): ?>
                        <li class="list-group-item">
                            <?php echo htmlspecialchars($item['name']); ?> 
                            (x<?php echo $item['quantity']; ?>)
                            <span class="float-end">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>Total Price: ₹<?php echo number_format($order['total_price'], 2); ?></strong></p>
            </div>

            <form action="checkout.php" method="post">
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="form-select" required>
                        <option value="" disabled selected>Select Payment Method</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="net_banking">Net Banking</option>
                        <option value="wallet">Wallet</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Complete Payment</button>
            </form>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
