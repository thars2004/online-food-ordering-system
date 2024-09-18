<?php
include 'includes/header.php';
$page_title = 'Manage Users';

// Pagination settings
$limit = 10; // Number of users per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Role filter
$roleFilter = isset($_POST['role']) ? mysqli_real_escape_string($conn, $_POST['role']) : '';

// Fetch roles for the filter dropdown
$rolesQuery = "SELECT DISTINCT role FROM users";
$rolesResult = mysqli_query($conn, $rolesQuery);
$roles = mysqli_fetch_all($rolesResult, MYSQLI_ASSOC);

// Fetch users based on selected role
$whereClause = $roleFilter ? "WHERE role='$roleFilter'" : '';
$usersQuery = "SELECT * FROM users $whereClause LIMIT $start, $limit";
$usersResult = mysqli_query($conn, $usersQuery);
$users = mysqli_fetch_all($usersResult, MYSQLI_ASSOC);

// Count total number of users for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM users $whereClause";
$totalResult = mysqli_query($conn, $totalQuery);
$total = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($total / $limit);
?>

<div class="container mt-5">
    <h2>Manage Users</h2>
    
    <!-- Role filter form -->
    <form method="POST" action="manage_users.php">
        <div class="mb-3">
            <label for="role" class="form-label">Select Role</label>
            <select class="form-select" id="role" name="role">
                <option value="">All Roles</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo htmlspecialchars($role['role']); ?>" <?php echo ($role['role'] == $roleFilter) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($role['role']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Users table -->
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No users found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="manage_users.php?page=<?php echo ($page - 1); ?>&role=<?php echo htmlspecialchars($roleFilter); ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="manage_users.php?page=<?php echo $i; ?>&role=<?php echo htmlspecialchars($roleFilter); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="manage_users.php?page=<?php echo ($page + 1); ?>&role=<?php echo htmlspecialchars($roleFilter); ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

<?php include 'includes/footer.php'; ?>
