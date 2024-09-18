<?php
include 'includes/header.php';
include 'includes/auth.php';
checkRole('customer');


if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. <a href='index.php'>Go back</a></p>";
    exit();
}

$total_amount = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Your Cart</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Food Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th> <!-- New column for individual checkout -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>₹<?php echo htmlspecialchars($item['price']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
                        <td>
                            <!-- Checkout form for individual items  -->
                            <form action="process_order.php" method="post">
                                <input type="hidden" name="food_item_id" value='<?php echo htmlspecialchars($item['id']); ?>'>
                                <input type="hidden" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>">
                                <input type="hidden" name="restaurant_id" value='<?php echo htmlspecialchars($item['restaurant_id']); ?>'>
                                <a href="remove_from_cart.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Remove</a> 
                                <button type="submit" class="btn btn-success btn-sm">Proceed to Checkout</button>
                            </form>
                        </td>
                    </tr>
                    <?php $total_amount += $item['price'] * $item['quantity']; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- <h4>Total Amount: ₹<?php echo $total_amount; ?></h4> -->
    </div>

    <?php include 'includes/footer.php'; ?>
