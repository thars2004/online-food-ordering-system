<?php
include '../includes/db.php';
include '../includes/auth.php';
checkRole('customer');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    
    // Update the status of the order to 'canceled'
    $updateQuery = "UPDATE orders SET status = 'canceled' WHERE id = $order_id";
    
    if ($conn->query($updateQuery) === TRUE) {
        // Redirect back to the order status page with a success message
        header('Location: order_status.php?message=Order+Canceled+Successfully');
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
