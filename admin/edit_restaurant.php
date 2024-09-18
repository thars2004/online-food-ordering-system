<?php
include 'includes/header.php';
$page_title = 'Edit Restaurant';

// Fetch restaurant details
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM restaurants WHERE id = '$id' AND deleted = 0";
    $result = mysqli_query($conn, $query);
    $restaurant = mysqli_fetch_assoc($result);
    
    if (!$restaurant) {
        die("Restaurant not found or has been deleted.");
    }
} else {
    die("Invalid ID.");
}

// Handle updating the restaurant
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_restaurant'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $opening_time = mysqli_real_escape_string($conn, $_POST['opening_time']);
    $closing_time = mysqli_real_escape_string($conn, $_POST['closing_time']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Handle file upload
    $image_filename = $restaurant['image']; // Retain old image by default
    if (isset($_FILES['image']['name']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_name = basename($_FILES['image']['name']);
        $target_dir = '../uploads/';
        $target_file = $target_dir . $image_name;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($image_tmp_name, $target_file)) {
            $image_filename = $image_name;
        } else {
            $error_msg = "Error uploading image.";
        }
    }

    // Update the restaurant in the database
    if (empty($error_msg)) {
        $query = "UPDATE restaurants SET name = '$name', address = '$address', phone = '$phone', open_time = '$opening_time', close_time = '$closing_time', description = '$description', image = '$image_filename' WHERE id = '$id'";
        if (mysqli_query($conn, $query)) {
            // Redirect with a success message
            header("Location: manage_restaurants.php?edit_success=1");
            exit();
        } else {
            $error_msg = "Error updating restaurant: " . mysqli_error($conn);
        }
    }
}
?>

<div class="container mt-5">
    <h2>Edit Restaurant</h2>

    <!-- Success or error messages -->
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Edit Restaurant Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Edit Restaurant Details</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="edit_restaurant.php?id=<?php echo htmlspecialchars($restaurant['id']); ?>" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Restaurant Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($restaurant['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($restaurant['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($restaurant['address']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($restaurant['phone']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="opening_time" class="form-label">Opening Time</label>
                    <input type="time" class="form-control" id="opening_time" name="opening_time" value="<?php echo htmlspecialchars($restaurant['open_time']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="closing_time" class="form-label">Closing Time</label>
                    <input type="time" class="form-control" id="closing_time" name="closing_time" value="<?php echo htmlspecialchars($restaurant['close_time']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image Upload (Leave blank to keep current image)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <?php if ($restaurant['image']): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($restaurant['image']); ?>" alt="Restaurant Image" style="width: 100px; height: auto;">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" name="update_restaurant">Update Restaurant</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
