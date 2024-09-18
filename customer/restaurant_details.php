<?php
include '../includes/db.php';
include 'includes/header.php';
include '../includes/auth.php';
checkRole('customer');


// Get restaurant ID from the query string
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $restaurant_id = $_GET['id'];

    // Fetch restaurant details from the database
    $restaurant_query = "SELECT * FROM restaurants WHERE id = $restaurant_id";
    $restaurant_result = mysqli_query($conn, $restaurant_query);
    $restaurant = mysqli_fetch_assoc($restaurant_result);

    // Fetch menu items for this restaurant
    $menu_query = "SELECT * FROM food_items WHERE restaurant_id = $restaurant_id";
    $menu_result = mysqli_query($conn, $menu_query);
} else {
    // If ID is not provided, redirect to the restaurant list page or show an error
    header("Location: restaurants.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?> - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
   
</head>
<body>
<div class="container mt-5">
    <h2><?php echo htmlspecialchars($restaurant['name']); ?></h2>
    <img src="../uploads/<?php echo htmlspecialchars($restaurant['image']); ?>" class="img-fluid restaurant-img" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">

    <p><?php echo htmlspecialchars($restaurant['description']); ?></p>
    <p>Location: <?php echo htmlspecialchars($restaurant['address']); ?></p>
    <p>Opening Hours: <?php echo date("g:i A", strtotime($restaurant['open_time'])); ?> - <?php echo date("g:i A", strtotime($restaurant['close_time'])); ?></p>

    <hr>
    <h3>Menu Items</h3>
    <div class="row">
        <?php
        if (mysqli_num_rows($menu_result) > 0) {
            // Loop through each menu item and display its details
            while ($menu_item = mysqli_fetch_assoc($menu_result)) {
        ?>
        <div class="col-md-4 menu-item">
            <div class="card">
                <img src="../uploads/<?php echo htmlspecialchars($menu_item['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($menu_item['name']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($menu_item['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($menu_item['description']); ?></p>
                    <p class="card-text">Price: ₹<?php echo htmlspecialchars($menu_item['price']); ?></p>
                </div>
            </div>
        </div>
        <?php
            }
        } else {
            echo "<p>No menu items available at this time.</p>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
