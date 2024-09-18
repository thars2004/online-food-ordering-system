<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swiggy</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery (optional, if you need for JS interactions) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome (for icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Style for active link */
        .nav-link.active {
            color: blue ;
            font-weight: bold;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Swiggy</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item me-3 <?php if ($page == 'home') echo 'active'; ?>">
                    <a class="nav-link" href="index.php">
                    <i class="fas fa-home"></i>Home</a>
                </li>
                <li class="nav-item me-3 <?php if ($page == 'View Deliveries') echo 'active'; ?>">
                    <a class="nav-link" href="view_deliveries.php">
                        <i class="fas fa-shopping-cart"></i> View Deliveries
                    </a>
                </li>
                <li class="nav-item me-3 <?php if ($page == 'Orders') echo 'active'; ?>">
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-box"></i> Orders
                    </a>
                </li>
                <li class="nav-item me-3 <?php if ($page == 'Profile') echo 'active'; ?>">
                    <a class="nav-link" href="profile.php">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                </li>
                
                <li class="nav-item me-3">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

