<?php
include '../includes/db.php';
include '../includes/auth.php';
checkRole('admin'); // Only allow admin users
$current_page = isset($current_page) ? $current_page : '';

// Start HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky">
                    <h4 class="text-center mb-4">Admin Menu</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" href="index.php">
                                <i class="bi bi-house-door"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'manage_users') ? 'active' : ''; ?>" href="manage_users.php">
                                <i class="bi bi-person"></i> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'manage_restaurants') ? 'active' : ''; ?>" href="manage_restaurants.php">
                                <i class="bi bi-restaurant"></i> Manage Restaurants
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'manage_food_items') ? 'active' : ''; ?>" href="manage_food_items.php">
                                <i class="bi bi-egg"></i> Manage Food Items
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'manage_orders') ? 'active' : ''; ?>" href="manage_orders.php">
                                <i class="bi bi-cart"></i> Manage Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'reports') ? 'active' : ''; ?>" href="reports.php">
                                <i class="bi bi-file-earmark-text"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'about') ? 'active' : ''; ?>" href="about.php">
                                <i class="bi bi-info-circle"></i> About
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main role="main" class="col-md-9 ms-sm-auto col-lg-10 px-4">
            <header class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?php echo isset($page_title) ? $page_title : 'Admin Dashboard'; ?></h1>
                <div class="d-flex align-items-center">
                    <span class="navbar-text me-3">
                        <span class="badge bg-primary"><?php echo $_SESSION['role']; ?></span> 
                        <span class="text-muted"><?php echo $_SESSION['name']; ?></span>
                    </span>
                    <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                </div>
            </header>
