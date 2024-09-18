<?php
include 'includes/header.php';
$page_title = 'Manage Food Items';

// Pagination settings
$limit = 10; // Number of food items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Handle adding a new food item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_food_item'])) {
    $restaurant_id = intval($_POST['restaurant_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    
    // Handle file upload
    $image_filename = '';
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

    // Insert the food item into the database
    if (empty($error_msg)) {
        $query = "INSERT INTO food_items (restaurant_id, name, description, price, image) 
                  VALUES ('$restaurant_id', '$name', '$description', '$price', '$image_filename')";
        if (mysqli_query($conn, $query)) {
            $success_msg = "Food item added successfully.";
        } else {
            $error_msg = "Error adding food item: " . mysqli_error($conn);
        }
    }
}

// Fetch restaurants for the dropdown
$restaurantsQuery = "SELECT id, name FROM restaurants WHERE deleted = 0";
$restaurantsResult = mysqli_query($conn, $restaurantsQuery);
$restaurants = mysqli_fetch_all($restaurantsResult, MYSQLI_ASSOC);

// Handle deleting a food item
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $query = "UPDATE food_items SET deleted = 1 WHERE id = $delete_id";
    if (mysqli_query($conn, $query)) {
        $success_msg = "Food item deleted successfully.";
    } else {
        $error_msg = "Error deleting food item: " . mysqli_error($conn);
    }
}

// Fetch total number of food items
$totalQuery = "SELECT COUNT(*) AS total FROM food_items WHERE deleted = 0";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRows = $totalRow['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch food items with pagination
$foodItemsQuery = "
    SELECT * FROM food_items WHERE deleted = 0
    LIMIT $start, $limit
";
$foodItemsResult = mysqli_query($conn, $foodItemsQuery);
$foodItems = mysqli_fetch_all($foodItemsResult, MYSQLI_ASSOC);
?>

<div class="container mt-5">
    <h2>Manage Food Items</h2>

    <!-- Success or error messages -->
    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php elseif (isset($_GET['edit_success']) && $_GET['edit_success'] == '1'): ?>
        <div class="alert alert-success">Food item edited successfully.</div>
    <?php elseif (isset($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Add Food Item Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Add New Food Item</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="manage_food_items.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="restaurant_id" class="form-label">Select Restaurant</label>
                    <select id="restaurant_id" name="restaurant_id" class="form-select" required>
                        <?php foreach ($restaurants as $restaurant): ?>
                            <option value="<?php echo htmlspecialchars($restaurant['id']); ?>">
                                <?php echo htmlspecialchars($restaurant['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Food Item Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description"></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image Upload</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary" name="add_food_item">Add Food Item</button>
            </form>
        </div>
    </div>

    <!-- Food Items List -->
    <div class="card">
        <div class="card-header">
            <h4>Food Items List</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Restaurant</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($foodItems) > 0): ?>
                        <?php foreach ($foodItems as $foodItem): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($foodItem['id']); ?></td>
                                <td>
                                    <?php
                                    $restaurant_id = htmlspecialchars($foodItem['restaurant_id']);
                                    $restaurantQuery = "SELECT name FROM restaurants WHERE id = $restaurant_id";
                                    $restaurantResult = mysqli_query($conn, $restaurantQuery);
                                    $restaurant = mysqli_fetch_assoc($restaurantResult);
                                    echo htmlspecialchars($restaurant['name']);
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($foodItem['name']); ?></td>
                                <td><?php echo htmlspecialchars($foodItem['description']); ?></td>
                                <td><?php echo htmlspecialchars($foodItem['price']); ?></td>
                                <td>
                                    <?php if ($foodItem['image']): ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($foodItem['image']); ?>" alt="Food Image" style="width: 100px; height: auto;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Actions (edit or delete) can be added here -->
                                    <a href="edit_food_item.php?id=<?php echo htmlspecialchars($foodItem['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="manage_food_items.php?delete_id=<?php echo htmlspecialchars($foodItem['id']); ?>" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No food items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav>
                <ul class="pagination">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="manage_food_items.php?page=<?php echo ($page - 1); ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="manage_food_items.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="manage_food_items.php?page=<?php echo ($page + 1); ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
