<?php
include 'includes/header.php';
include '../includes/auth.php';
checkRole('customer');


$amount = isset($_GET['amount']) ? $_GET['amount'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body>
    <div class="container mt-5">
        <h2>Thank you for your order!</h2>
        <p>Your total amount is ₹<?php echo htmlspecialchars($amount); ?>.</p>
        <a href="index.php" class="btn btn-primary">Go Back to Home</a>
    </div>
</body>
</html>
