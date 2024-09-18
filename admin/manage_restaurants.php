<?php
include 'includes/header.php';
$page_title = 'Manage Restaurants';

// Pagination settings
$limit = 10; // Number of restaurants per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Handle adding a new restaurant
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_restaurant'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $opening_time = mysqli_real_escape_string($conn, $_POST['opening_time']);
    $closing_time = mysqli_real_escape_string($conn, $_POST['closing_time']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
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

    // Insert the restaurant into the database
    if (empty($error_msg)) {
        $query = "INSERT INTO restaurants (name, address, phone, open_time, close_time, description, image) 
                  VALUES ('$name', '$address', '$phone', '$opening_time', '$closing_time', '$description', '$image_filename')";
        if (mysqli_query($conn, $query)) {
            $success_msg = "Restaurant added successfully.";
        } else {
            $error_msg = "Error adding restaurant: " . mysqli_error($conn);
        }
    }
}

// Handle deleting a restaurant (setting deleted status to 1)
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $query = "UPDATE restaurants SET deleted = 1 WHERE id = '$delete_id'";
    if (mysqli_query($conn, $query)) {
        $success_msg = "Restaurant deactivated successfully.";
    } else {
        $error_msg = "Error deactivating restaurant: " . mysqli_error($conn);
    }
}

// Fetch total number of restaurants for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM restaurants WHERE deleted = 0";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRows = $totalRow['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch restaurants that are not deleted with pagination
$restaurantsQuery = "SELECT * FROM restaurants WHERE deleted = 0 LIMIT $start, $limit";
$restaurantsResult = mysqli_query($conn, $restaurantsQuery);
$restaurants = mysqli_fetch_all($restaurantsResult, MYSQLI_ASSOC);
?>

<div class="container mt-5">
    <h2>Manage Restaurants</h2>

    <!-- Success or error messages -->
    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php elseif (isset($_GET['edit_success']) && $_GET['edit_success'] == '1'): ?>
        <div class="alert alert-success">Restaurant edited successfully.</div>
    <?php elseif (isset($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Add Restaurant Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Add New Restaurant</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="manage_restaurants.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Restaurant Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="mb-3">
                    <label for="opening_time" class="form-label">Opening Time</label>
                    <input type="time" class="form-control" id="opening_time" name="opening_time" required>
                </div>
                <div class="mb-3">
                    <label for="closing_time" class="form-label">Closing Time</label>
                    <input type="time" class="form-control" id="closing_time" name="closing_time" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image Upload</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary" name="add_restaurant">Add Restaurant</button>
            </form>
        </div>
    </div>

    <!-- Restaurants List -->
    <div class="card">
        <div class="card-header">
            <h4>Restaurant List</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Opening Time</th>
                        <th>Closing Time</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($restaurants) > 0): ?>
                        <?php foreach ($restaurants as $restaurant): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($restaurant['id']); ?></td>
                                <td><?php echo htmlspecialchars($restaurant['name']); ?></td>
                                <td><?php echo htmlspecialchars($restaurant['address']); ?></td>
                                <td><?php echo htmlspecialchars($restaurant['phone']); ?></td>
                                <td><?php echo htmlspecialchars($restaurant['open_time']); ?></td>
                                <td><?php echo htmlspecialchars($restaurant['close_time']); ?></td>
                                <td><?php echo htmlspecialchars($restaurant['description']); ?></td>
                                <td>
                                    <?php if ($restaurant['image']): ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($restaurant['image']); ?>" alt="Restaurant Image" style="width: 100px; height: auto;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_restaurant.php?id=<?php echo htmlspecialchars($restaurant['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="manage_restaurants.php?delete_id=<?php echo htmlspecialchars($restaurant['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to deactivate this restaurant?');">Deactivate</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No restaurants found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav>
                <ul class="pagination">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="manage_restaurants.php?page=<?php echo ($page - 1); ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="manage_restaurants.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="manage_restaurants.php?page=<?php echo ($page + 1); ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
