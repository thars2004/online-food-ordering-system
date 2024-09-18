<?php
include 'includes/header.php';
$page_title = 'Dashboard';

// Fetch data from the database
include '../includes/db.php';

// Define your queries to get the necessary data
$queries = [
    'total_customers' => "SELECT COUNT(*) AS total_customers FROM users Where role='customer'",
    'total_deliveries' => "SELECT COUNT(*) AS total_deliveries FROM users Where role='delivery'",
    'orders_delivered' => "SELECT COUNT(*) AS orders_delivered FROM orders WHERE status = 'delivered'",
    'orders_canceled' => "SELECT COUNT(*) AS orders_canceled FROM orders WHERE status = 'canceled'",
    'total_orders' => "SELECT COUNT(*) AS total_orders FROM orders",
    'total_restaurants' => "SELECT COUNT(*) AS total_restaurants FROM restaurants",
    'total_food_items' => "SELECT COUNT(*) AS total_food_items FROM food_items",
];

// Execute queries and store results
$data = [];
foreach ($queries as $key => $query) {
    $result = mysqli_query($conn, $query);
    $data[$key] = mysqli_fetch_assoc($result)[str_replace('total_', 'total_', $key)];
}

// Close the database connection
mysqli_close($conn);
?>

<div class="container-fluid">
    <div class="row">
        <h2>Dashboard</h2>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Customers</h5>
                        <p class="card-text"><?php echo number_format($data['total_customers']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Delivery Person</h5>
                        <p class="card-text"><?php echo number_format($data['total_deliveries']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Orders Delivered</h5>
                        <p class="card-text"><?php echo number_format($data['orders_delivered']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Orders Canceled</h5>
                        <p class="card-text"><?php echo number_format($data['orders_canceled']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <p class="card-text"><?php echo number_format($data['total_orders']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Restaurants</h5>
                        <p class="card-text"><?php echo number_format($data['total_restaurants']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Food Items</h5>
                        <p class="card-text"><?php echo number_format($data['total_food_items']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
