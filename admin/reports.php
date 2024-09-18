<?php
include 'includes/header.php';
$page_title = 'Orders Report';

// Initialize variables
$startDate = '';
$endDate = '';
$orderDetails = [];

// Pagination settings
$limit = 10; // Number of orders per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $startDate = $_POST['start_date'] . ' 00:00:00';
    $endDate = $_POST['end_date'] . ' 23:59:59';


    // Validate date inputs
    if (!empty($startDate) && !empty($endDate)) {
        // Fetch orders within the specified date range
        $query = "
    SELECT 
        orders.id AS order_id,
        orders.total_price,
        orders.status AS order_status,
        orders.created_at AS order_date,
        users.name AS customer_name,
        restaurants.name AS restaurant_name,
        food_items.name AS food_name,
        order_items.quantity,
        order_items.price AS food_price,
        (order_items.quantity * order_items.price) AS item_total_price,
        delivery.status AS delivery_status,
        delivery.delivery_time,
        delivery_persons.name AS delivery_person_name,
        delivery_persons.id AS delivery_person_id
    FROM orders
    JOIN users ON orders.user_id = users.id
    JOIN order_items ON orders.id = order_items.order_id
    JOIN food_items ON order_items.food_item_id = food_items.id
    JOIN restaurants ON orders.restaurant_id = restaurants.id
    LEFT JOIN delivery ON orders.id = delivery.order_id
    LEFT JOIN users AS delivery_persons ON delivery.delivery_person_id = delivery_persons.id
    ORDER BY orders.created_at DESC
    LIMIT $start, $limit
";

        $result = mysqli_query($conn, $query);

        // Check if the query was successful and if any results were returned
        if ($result && mysqli_num_rows($result) > 0) {
            $orderDetails = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $orderDetails = [];
        }

        // Fetch total number of orders for the date range
        $totalQuery = "
            SELECT COUNT(DISTINCT orders.id) AS total
            FROM orders
            JOIN order_items ON orders.id = order_items.order_id
            WHERE orders.created_at BETWEEN '$startDate' AND '$endDate'
        ";
        $totalResult = mysqli_query($conn, $totalQuery);
        $totalRow = mysqli_fetch_assoc($totalResult);
        $totalRows = $totalRow['total'];
        $totalPages = ceil($totalRows / $limit);
    } else {
        echo "<script>alert('Please enter both start and end dates.');</script>";
    }
}
?>

<div class="container mt-5">
    <h2>Orders Report</h2>
    <div class="mb-3">
    <form method="post" action="reports.php" class="mb-4">
        
        <div class="form-group">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Generate Report</button>
    </form>
</div>

    <?php if (!empty($orderDetails)): ?>
        <div class="card">
            <div class="card-header">
                <h4>Order Report from <?php echo htmlspecialchars($startDate); ?> to <?php echo htmlspecialchars($endDate); ?></h4>
            </div>
            <div class="card-body">
            <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Restaurant</th>
                            <th>Food Item</th>
                            <th>Quantity</th>
                            <!-- <th>Price</th> -->
                            <th>Total Price</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Delivery Status</th> <!-- Updated column header -->
                            <th>Delivery Time</th> <!-- Updated column header -->
                            <th>Delivery Person</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderDetails as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['restaurant_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['food_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                <td>₹<?php echo htmlspecialchars($order['food_price']); ?></td>
                                <!-- <td>₹<?php echo htmlspecialchars($order['item_total_price']); ?></td> -->
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                                <td><?php echo htmlspecialchars($order['delivery_status']) ?: 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($order['delivery_time']) ?: 'N/A'; ?></td>
                                <td>
                                    <?php 
                                        if (!empty($order['delivery_person_name'])) {
                                            echo htmlspecialchars($order['delivery_person_name']) . " (ID: " . htmlspecialchars($order['delivery_person_id']) . ")";
                                        } else {
                                            echo "N/A";
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="reports.php?page=<?php echo ($page - 1); ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="reports.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="reports.php?page=<?php echo ($page + 1); ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <div class="alert alert-info">No orders found for the selected date range.</div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
