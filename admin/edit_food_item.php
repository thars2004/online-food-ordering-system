<?php
include 'includes/header.php';
$page_title = 'Edit Food Item';

$id = intval($_GET['id']);

// Handle updating a food item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_food_item'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $image_filename = $_POST['current_image']; // Retain old image by default

    // Handle file upload
    if (isset($_FILES['image']['name']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_name = basename($_FILES['image']['name']);
        $target_dir = '../uploads/';
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($image_tmp_name, $target_file)) {
            $image_filename = $image_name;
        } else {
            $error_msg = "Error uploading image.";
        }
    }

    // Update the food item in the database
    if (empty($error_msg)) {
        $query = "UPDATE food_items SET name = '$name', description = '$description', price = '$price', image = '$image_filename' WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            header("Location: manage_food_items.php?edit_success=1");
            exit();
        } else {
            $error_msg = "Error updating food item: " . mysqli_error($conn);
        }
    }
}

// Fetch food item details
$query = "SELECT * FROM food_items WHERE id = $id AND deleted = 0";
$result = mysqli_query($conn, $query);
$foodItem = mysqli_fetch_assoc($result);

if (!$foodItem) {
    $error_msg = "Food item not found or has been deleted.";
}

?>

<div class="container mt-5">
    <h2>Edit Food Item</h2>

    <!-- Error message -->
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Edit Food Item Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Edit Food Item</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="edit_food_item.php?id=<?php echo htmlspecialchars($foodItem['id']); ?>" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Food Item Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($foodItem['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($foodItem['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($foodItem['price']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image Upload (Leave blank to keep current image)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <?php if ($foodItem['image']): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($foodItem['image']); ?>" alt="Food Image" style="width: 100px; height: auto;">
                    <?php endif; ?>
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($foodItem['image']); ?>">
                </div>
                <button type="submit" class="btn btn-primary" name="update_food_item">Update Food Item</button>
            </form>
        </div>
    </div>
</div>


<?php include 'includes/footer.php'; ?>
