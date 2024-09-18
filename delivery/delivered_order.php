<?php
include '../includes/db.php';
include 'includes/auth.php';
checkRole('delivery');

if (isset($_POST['delivered'])) {
    $orderId = $_POST['order_id'];
    $deliveryPersonId = $_SESSION['user_id']; // Assuming you have the delivery person ID in the session
    
    // Update order status to 'delivered'
    $updateQuery = "UPDATE orders SET status = 'delivered' WHERE id = ?";
    
    // Prepare the statement to avoid SQL injection
    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("i", $orderId);
        
        if ($stmt->execute()) {
            // Insert delivery details
            $insertQuery = "INSERT INTO delivery (order_id, delivery_person_id, status) VALUES (?, ?, 'delivered')";
            
            if ($stmtInsert = $conn->prepare($insertQuery)) {
                $stmtInsert->bind_param("ii", $orderId, $deliveryPersonId);
                
                if ($stmtInsert->execute()) {
                    echo "<script>alert('Order status updated to Delivered'); window.location.href='view_deliveries.php';</script>";
                } else {
                    echo "<script>alert('Failed to insert delivery details');</script>";
                }
            } else {
                echo "<script>alert('Failed to prepare delivery insert statement');</script>";
            }
        } else {
            echo "<script>alert('Failed to update order status');</script>";
        }
        
        $stmt->close();
    } else {
        echo "<script>alert('Failed to prepare update statement');</script>";
    }
}
?>
