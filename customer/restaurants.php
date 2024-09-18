<?php
include '../includes/db.php';
include 'includes/header.php';
include '../includes/auth.php';
checkRole('customer');


// Fetch restaurant data from the database
$query = "SELECT * FROM restaurants where deleted=0"; // Replace with your table name
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants - Swiggy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        /* Ensure cards and rows display properly */
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-body {
            flex-grow: 1;
        }

        .card-img-top {
            max-height: 200px;
            object-fit: cover;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col-md-4 {
            display: flex;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Restaurants Near You</h2>
    <div class="row">
        <?php
        if (mysqli_num_rows($result) > 0) {
            // Loop through each restaurant and display its details
            while ($restaurant = mysqli_fetch_assoc($result)) {
        ?>
        <div class="col-md-4 d-flex">
            <div class="card w-100">
                <img src="../uploads/<?php echo htmlspecialchars($restaurant['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" >
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($restaurant['description']); ?></p>
                    <p class="card-text">Location: <?php echo htmlspecialchars($restaurant['address']); ?></p>
                    <p class="card-text">Opening Hours: <?php echo date("g:i A", strtotime($restaurant['open_time'])); ?> - <?php echo date("g:i A", strtotime($restaurant['close_time'])); ?></p>
                    <a href="restaurant_details.php?id=<?php echo $restaurant['id']; ?>" class="btn btn-primary">View Menu</a>
                </div>
            </div>
        </div>
        <?php
            }
        } else {
            echo "<p>No restaurants available at this time.</p>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
