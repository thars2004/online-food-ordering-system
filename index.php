<?php
include 'includes/db.php';
include 'includes/header.php';
// include 'includes/auth.php';

// Fetch only undeleted restaurant data
$restaurantsQuery = "SELECT * FROM restaurants WHERE deleted = 0";
$restaurantsResult = mysqli_query($conn, $restaurantsQuery);
$restaurants = mysqli_fetch_all($restaurantsResult, MYSQLI_ASSOC);

// Fetch only undeleted food items data
$foodItemsQuery = "SELECT * FROM food_items WHERE deleted = 0";
$foodItemsResult = mysqli_query($conn, $foodItemsQuery);
$foodItems = mysqli_fetch_all($foodItemsResult, MYSQLI_ASSOC);

// Convert opening and closing times to 12-hour AM/PM format
function convertToAmPm($time) {
    return date("g:i A", strtotime($time));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">

        <h2>Featured Restaurants</h2>
        <div class="row">
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="uploads/<?php echo htmlspecialchars($restaurant['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($restaurant['description']); ?></p>
                            <p class="card-text"><small class="text-muted">Opening Time: <?php echo htmlspecialchars(convertToAmPm($restaurant['open_time'])); ?> | Closing Time: <?php echo htmlspecialchars(convertToAmPm($restaurant['close_time'])); ?></small></p>
                            <a href="restaurant_details.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-primary">View Menu</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>Popular Food Items</h2>
<div class="row">
    <?php foreach ($foodItems as $item): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                    
                    <!-- Price and button on the same line -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="price-tag"><?php echo htmlspecialchars($item['price']); ?> INR</div>
                        <a href="order_food.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">Order</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


    </div>

    <?php include 'includes/footer.php'; ?>
