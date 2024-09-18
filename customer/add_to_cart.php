<?php
include '../includes/db.php';
include '../includes/auth.php';
checkRole('customer'); // Ensure only customers can add items to the cart

// Fetch the food item by ID
if (isset($_GET['id'])) {
    $foodItemId = $_GET['id'];
    
    // Query to get the food item details
    $foodItemQuery = "SELECT * FROM food_items WHERE id = ? AND deleted = 0";
    $stmt = mysqli_prepare($conn, $foodItemQuery);
    mysqli_stmt_bind_param($stmt, 'i', $foodItemId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $foodItem = mysqli_fetch_assoc($result);

    if ($foodItem) {
        // Initialize cart if not already set
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add the item to the cart (you can check for duplicates)
        $cartItem = [
            'id' => $foodItem['id'],
            'name' => $foodItem['name'],
            'price' => $foodItem['price'],
            'quantity' => 1,
            'restaurant_id' =>  $foodItem['restaurant_id'],
        ];

        // Check if item already exists in the cart
        $itemExists = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $cartItem['id']) {
                $item['quantity']++; // Increase quantity if already in the cart
                $itemExists = true;
                break;
            }
        }

        // If item doesn't exist, add new item to the cart
        if (!$itemExists) {
            $_SESSION['cart'][] = $cartItem;
        }

        // Redirect back to the home page or a cart page
        header('Location: cart.php');
        exit();
    } else {
        // If food item doesn't exist or is deleted
        echo "Food item not found.";
    }
} else {
    echo "Invalid food item.";
}
?>
