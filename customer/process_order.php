<?php
include '../includes/db.php'; // Database connection
include 'includes/header.php';
include '../includes/auth.php';
checkRole('customer');

$errors = [];
$success = false;

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $errors[] = "You must be logged in to place an order.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_item_id'], $_POST['quantity'], $_POST['restaurant_id'])) {
    $food_item_id = intval($_POST['food_item_id']);
    $quantity = intval($_POST['quantity']);
    $restaurant_id = intval($_POST['restaurant_id']);
    $user_id = $_SESSION['user_id'];

    if ($food_item_id > 0 && $quantity > 0 && $restaurant_id > 0) {
        // Fetch food item details using prepared statement
        $stmt = $conn->prepare("SELECT * FROM food_items WHERE id = ? AND restaurant_id = ? AND deleted = 0");
        $stmt->bind_param("ii", $food_item_id, $restaurant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $foodItem = $result->fetch_assoc();

        if ($foodItem) {
            // Calculate total cost
            $total_cost = $foodItem['price'] * $quantity;

            // Insert into `orders` table
            $stmt_order = $conn->prepare("INSERT INTO orders (user_id, restaurant_id, total_price) VALUES (?, ?, ?)");
            $stmt_order->bind_param("iid", $user_id, $restaurant_id, $total_cost);

            if ($stmt_order->execute()) {
                $order_id = $stmt_order->insert_id; // Get the order ID

                // Insert into `order_items` table
                $stmt_order_items = $conn->prepare("INSERT INTO order_items (order_id, food_item_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt_order_items->bind_param("iiid", $order_id, $food_item_id, $quantity, $total_cost);
                
                if ($stmt_order_items->execute()) {
                    // Insert into `transactions` table
                    $payment_method = 'Online'; // Replace with actual payment method if applicable
                    $transaction_date = date('Y-m-d H:i:s'); // Current timestamp

                    $stmt_transaction = $conn->prepare("INSERT INTO transactions (order_id, user_id, amount, payment_method, transaction_date) VALUES (?, ?, ?, ?, ?)");
                    $stmt_transaction->bind_param("iiiss", $order_id, $user_id, $total_cost, $payment_method, $transaction_date);

                    if ($stmt_transaction->execute()) {
                        $success = true;
                    } else {
                        $errors[] = "Error recording transaction.";
                    }
                } else {
                    $errors[] = "Error adding items to the order.";
                }
            } else {
                $errors[] = "Failed to place the order.";
            }
        } else {
            $errors[] = "Food item not found.";
        }
    } else {
        $errors[] = "Invalid quantity, food item, or restaurant.";
    }
} else {
    $errors[] = "No order data submitted.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <?php if ($success): ?>
            <div class="alert alert-success">
                <h4 class="alert-heading">Order Placed Successfully!</h4>
                <p>Thank you for your order. Your order has been placed successfully.</p>
                <a href="index.php" class="btn btn-primary">Return to Home</a>
            </div>
        <?php elseif (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h4 class="alert-heading">Order Error</h4>
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
                <a href="order_food.php" class="btn btn-primary">Return to Order</a>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <h4 class="alert-heading">No Order Data</h4>
                <p>No order data was submitted. Please make sure to select a food item and quantity.</p>
                <a href="index.php" class="btn btn-primary">Return to Home</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
