<?php
$page = 'home';

include '../includes/db.php';
include 'includes/header.php';
include 'includes/auth.php';
checkRole('delivery');

// Fetch delivery-specific data (e.g., assigned deliveries)
$delivery_person_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-7 days')); // Example: Last 7 days
$end_date = date('Y-m-d');

// Fetch today's orders by status for the pie chart
$statusQuery = "
    SELECT 
        o.status AS order_status, 
        COUNT(*) AS order_count
    FROM orders o
    WHERE o.delivery_person_id = ?
    AND DATE(o.created_at) = ?
    GROUP BY o.status
";

// Prepare the statement to avoid SQL injection
$stmt = $conn->prepare($statusQuery);
$stmt->bind_param("is", $delivery_person_id, $today);
$stmt->execute();
$statusResult = $stmt->get_result();

// Initialize all possible statuses
$allStatuses = ['take over', 'picked up', 'on the way', 'delivered'];
$status_counts = array_fill_keys($allStatuses, 0);

// Populate the status counts from the query result
while ($row = $statusResult->fetch_assoc()) {
    $status_counts[$row['order_status']] = (int) $row['order_count'];
}

// Prepare JSON data for the chart
$status_labels_json = json_encode(array_keys($status_counts));
$status_counts_json = json_encode(array_values($status_counts));


// Fetch the number of delivered orders for each date for the bar chart
$chartQuery = "
    SELECT 
        DATE(o.created_at) AS order_date, 
        COUNT(*) AS delivered_orders
    FROM orders o
    WHERE o.delivery_person_id = $delivery_person_id
    AND o.status = 'delivered'
    AND DATE(o.created_at) BETWEEN '$start_date' AND '$end_date'
    GROUP BY DATE(o.created_at)
    ORDER BY DATE(o.created_at)
";
$chartResult = $conn->query($chartQuery);

$dates = [];
$delivered_orders = [];

while ($row = $chartResult->fetch_assoc()) {
    $dates[] = $row['order_date'];
    $delivered_orders[] = (int) $row['delivered_orders'];
}

// Convert PHP arrays to JSON for use in JavaScript
// $status_labels_json = json_encode($status_labels);
// $status_counts_json = json_encode($status_counts);
$dates_json = json_encode($dates);
$delivered_orders_json = json_encode($delivered_orders);

// Initialize all statuses with 0 counts
$allStatuses = ['take over', 'picked up', 'on the way', 'delivered'];
$status_counts = array_fill_keys($allStatuses, 0);

// Populate status counts from query result
while ($row = $statusResult->fetch_assoc()) {
    $status_counts[$row['order_status']] = (int) $row['order_count'];
}

$status_labels_json = json_encode(array_keys($status_counts));
$status_counts_json = json_encode(array_values($status_counts));

// Fetch today's orders, total collection, and delivered orders for the chart
$summaryQuery = "
    SELECT 
        COUNT(*) AS total_orders, 
        SUM(o.total_price) AS total_collection,
        SUM(CASE WHEN o.status = 'delivered' THEN 1 ELSE 0 END) AS delivered_orders
    FROM orders o 
    WHERE o.delivery_person_id = ?
    AND DATE(o.created_at) = ?
";
// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare($summaryQuery);
$stmt->bind_param("is", $delivery_person_id, $today);
$stmt->execute();
$summaryResult = $stmt->get_result();
$summary = $summaryResult->fetch_assoc();

// Fetch active deliveries (non-delivered orders)
$query = "SELECT o.id, r.name AS restaurant_name, u.address AS customer_address, o.total_price, o.status AS order_status 
          FROM orders o
          JOIN restaurants r ON o.restaurant_id = r.id
          JOIN users u ON o.user_id = u.id
          WHERE o.delivery_person_id = ? 
          AND o.status != 'delivered'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $delivery_person_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js Library -->
    <style>
        #statusChartContainer {
            max-width: 400px;
            margin: auto;
            height: 300px; /* Add this if the height is too small */
        }

    </style>
</head>
<body>
    <div class="container mt-5">
        

        <!-- Table to Display Active Deliveries -->
        <h2>Your Active Deliveries</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Restaurant</th>
                        <th>Customer Address</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['restaurant_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_address']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_price']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                        <td ><a href="view_deliveries.php" class="btn btn-primary">View Details</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No active deliveries</p>
        <?php endif; ?>
    
    <input type="hidden" id="pickedUpOrders" value='<?php echo htmlspecialchars($summary['picked_up_orders']); ?>'>
    <input type="hidden" id="onTheWayOrders" value='<?php echo htmlspecialchars($summary['on_the_way_orders']); ?>'>
    <input type="hidden" id="deliveredOrders" value='<?php echo htmlspecialchars($summary['delivered_orders']); ?>'>

     <!-- Smaller Pie Chart Container -->
        <!-- <h2>Today's Order Status</h2>
        <div id="statusChartContainer">
            <canvas id="statusChart"></canvas>
        </div> -->

        <!-- Bar Chart for Delivered Orders by Date -->
        <h2>Orders Delivered by Date</h2>
        <div style="max-width: 800px;">
            <canvas id="deliveryChart" width="800" height="400"></canvas>
        </div>
    </div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // var ctxStatus = document.getElementById('statusChart').getContext('2d');
    var ctxDelivery = document.getElementById('deliveryChart').getContext('2d');

//      // Data for the Pie/Bar Chart (Order Status)
//     var statusLabels = <?php echo $status_labels_json; ?>;
//     var statusCounts = <?php echo $status_counts_json; ?>;

//     // Check if there are data to plot
//     if (statusLabels.length === 0 || statusCounts.length === 0) {
//         console.error("No data found for the order status chart!");
//         return;
//     }

//     // Create the Pie/Bar chart for Order Status
//     new Chart(ctxStatus, {
//         type: 'bar', // You can change this to 'pie' if you prefer
//         data: {
//             labels: statusLabels,
//             datasets: [{
//                 label: 'Order Status',
//                 data: statusCounts,
//                 backgroundColor: ['#007bff', '#ffc107', '#28a745', '#dc3545'], // Colors for each status
//                 borderColor: ['#007bff', '#ffc107', '#28a745', '#dc3545'],
//                 borderWidth: 1
//             }]
//         },
//         options: {
//             responsive: true,
//             scales: {
//                 x: {
//                     beginAtZero: true,
//                     title: {
//                         display: true,
//                         text: 'Order Status'
//                     }
//                 },
//                 y: {
//                     beginAtZero: true,
//                     title: {
//                         display: true,
//                         text: 'Number of Orders'
//                     }
//                 }
//             }
//         }
//     });
//     console.log("Status Labels:", statusLabels);
// console.log("Status Counts:", statusCounts);

    // Data for the Point-to-Point Chart (Delivered Orders by Date)
    var dates = <?php echo $dates_json; ?>;
    var deliveredOrders = <?php echo $delivered_orders_json; ?>;

    new Chart(ctxDelivery, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Delivered Orders',
                data: deliveredOrders,
                backgroundColor: '#28a745',
                borderColor: '#218838',
                borderWidth: 2,
                fill: false, // Prevent filling under the line
                pointRadius: 5, // Add dot markers
                pointBackgroundColor: '#ff6384', // Color for the dots
                pointBorderColor: '#ff6384',
                pointStyle: 'circle', // Style for the dots
                tension: 0.4, // Smooth the lines slightly
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Delivered Orders'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });
});
</script>




    </div>
    
    <?php include 'includes/footer.php'; ?>
