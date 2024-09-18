<?php

include '../includes/db.php';
include '../includes/auth.php';
checkRole('customer'); // Ensure only customers can add items to the cart


// Check if the item ID is provided and cart is set
if (isset($_GET['id']) && isset($_SESSION['cart'])) {
    $foodItemId = $_GET['id'];

    // Remove the item from the cart
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $foodItemId) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }

    // Reindex the array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Redirect back to the cart
header('Location: cart.php');
exit();
?>