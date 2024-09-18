<?php
include 'includes/db.php';
include 'includes/header.php';

// Initialize variables
$errors = [];
$success = false;

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    $errors[] = "You must be logged in to place an order.";
} else {
    // Get user ID
    $user_id = $_SESSION['user_id'];

    // Check if form data is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_items']) && is_array($_POST['food_items'])) {
        // Initialize total cost
        $total_cost = 0;
        $order_items = [];
        
        // Loop through each food item
        foreach ($_POST['food_items'] as $food_id => $quantity) {
            $food_id = intval($food_id);
            $quantity = intval($quantity);

            if ($food_id > 0 && $quantity > 0) {
                // Fetch food item details
                $foodQuery = "SELECT * FROM food_items WHERE id = $food_id AND deleted = 0";
                $foodResult = mysqli_query($conn, $foodQuery);
                $foodItem = mysqli_fetch_assoc($foodResult);

                if ($foodItem) {
                    // Calculate cost and add to total cost
                    $item_total = $foodItem['price'] * $quantity;
                    $total_cost += $item_total;

                    // Store order item details
                    $order_items[] = [
                        'food_id' => $food_id,
                        'quantity' => $quantity,
                        'total_cost' => $item_total
                    ];
                } else {
                    $errors[] = "Food item with ID $food_id not found.";
                }
            } else {
                $errors[] = "Invalid food ID or quantity for item ID $food_id.";
            }
        }

        if (empty($errors)) {
            // Insert order into database
            $orderQuery = "INSERT INTO orders (user_id, total_cost) VALUES ($user_id, $total_cost)";
            if (mysqli_query($conn, $orderQuery)) {
                // Get the last inserted order ID
                $order_id = mysqli_insert_id($conn);

                // Insert each order item into the database
                foreach ($order_items as $item) {
                    $orderItemQuery = "INSERT INTO order_items (order_id, food_id, quantity, total_cost) VALUES ($order_id, {$item['food_id']}, {$item['quantity']}, {$item['total_cost']})";
                    if (!mysqli_query($conn, $orderItemQuery)) {
                        $errors[] = "Error adding item to order: " . mysqli_error($conn);
                    }
                }

                if (empty($errors)) {
                    $success = true;
                }
            } else {
                $errors[] = "Error placing the order: " . mysqli_error($conn);
            }
        }
    } else {
        $errors[] = "No order data was submitted.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
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

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
