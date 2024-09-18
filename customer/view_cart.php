<?php
include 'includes/header.php';
include '../includes/auth.php';
checkRole('customer');

// Check if the cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Empty Cart</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/style.css" rel="stylesheet">
        <style>
            .empty-cart-container {
                margin-top: 100px;
                text-align: center;
            }
            .empty-cart-alert {
                background-color: #f8d7da;
                color: #721c24;
                padding: 20px;
                border-radius: 5px;
                display: inline-block;
            }
            .empty-cart-btn {
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container empty-cart-container">
            <div class="empty-cart-alert">
                <h3>Your cart is empty!</h3>
                <p>It looks like you haven't added any items to your cart yet.</p>
                <a href="index.php" class="btn btn-primary empty-cart-btn">Browse Foods</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>
