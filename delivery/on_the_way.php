<?php
include '../includes/db.php';
include 'includes/auth.php';
checkRole('delivery');

if (isset($_POST['on_the_way'])) {
    $orderId = $_POST['order_id'];
    
    // Update order status to 'on_the_way'
    $updateQuery = "UPDATE orders SET status = 'on_the_way' WHERE id = $orderId";
    
    if ($conn->query($updateQuery)) {
        echo "<script>alert('Order status updated to On the Way'); window.location.href='view_deliveries.php';</script>";
    } else {
        echo "<script>alert('Failed to update order status');</script>";
    }
}
?>
