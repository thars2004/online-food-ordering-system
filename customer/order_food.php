<?php
include '../includes/db.php';
include 'includes/header.php';
include '../includes/auth.php';
checkRole('customer');


// Set session variables for the order details
// $_SESSION['food_item_id'] = $_POST['food_item_id'];
// $_SESSION['quantity'] = $_POST['quantity'];

if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: ../login.php");
    exit();
}

// Fetch the selected food item based on the ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $food_item_id = $_GET['id'];

    // Fetch the selected food item
    $foodQuery = "SELECT * FROM food_items WHERE id = $food_item_id AND deleted = 0";
    $foodResult = mysqli_query($conn, $foodQuery);
    $foodItem = mysqli_fetch_assoc($foodResult);

    // Fetch other available food items
    $otherItemsQuery = "SELECT * FROM food_items WHERE id != $food_item_id AND deleted = 0";
    $otherItemsResult = mysqli_query($conn, $otherItemsQuery);
    $otherItems = mysqli_fetch_all($otherItemsResult, MYSQLI_ASSOC);
} else {
    // If ID is not provided, redirect to the home page
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Food - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .food-item-img {
            max-height: 300px;
            object-fit: cover;
        }

        .order-summary {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .order-summary h4 {
            margin-top: 0;
        }

        .additional-items img {
            max-height: 150px;
            object-fit: cover;
        }

        .additional-items .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <!-- Selected Food Item Details -->
            <div class="col-md-6">
                <h2><?php echo htmlspecialchars($foodItem['name']); ?></h2>
                <img src="../uploads/<?php echo htmlspecialchars($foodItem['image']); ?>" class="img-fluid food-item-img" alt="<?php echo htmlspecialchars($foodItem['name']); ?>">
                <div class="order-summary">
                    <h4>Order Summary</h4>
                    <p><?php echo htmlspecialchars($foodItem['description']); ?></p>
                    <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($foodItem['price']); ?></p>
                    <form action="process_order.php" method="post">
                        <input type="hidden" name="food_item_id" value="<?php echo $foodItem['id']; ?>">
                        <input type="hidden" name="restaurant_id" value="<?php echo $foodItem['restaurant_id']; ?>">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" min="1" value="1">
                        </div>
                        <button type="submit" class="btn btn-primary">Place Order</button>
                    </form>
                </div>
            </div>

            <!-- Additional Food Items -->
            <div class="col-md-6">
                <h2>Other Popular Food Items</h2>
                <div class="row additional-items">
                    <?php foreach ($otherItems as $item): ?>
                        <div class="col-md-6">
                            <div class="card">
                                <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <p class="card-text">₹<?php echo htmlspecialchars($item['price']); ?></p>
                                    <a href="order_food.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">Order This</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
