<?php
include '../includes/db.php'; // Database connection
include 'includes/header.php';
include '../includes/auth.php';
checkRole('customer');


// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, phone, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        /* General styles */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .profile-container {
            margin-top: 60px;
            max-width: 700px;
            margin: auto;
        }

        .card {
            background-color: #ffffff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            padding: 30px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-header h2 {
            color: #007bff;
            font-weight: bold;
        }

        .profile-detail {
            display: flex;
            justify-content: space-between;
            font-size: 1.1em;
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .profile-detail:last-child {
            border-bottom: none;
        }

        .profile-label {
            font-weight: 600;
            color: #495057;
        }

        .profile-value {
            font-weight: 400;
            color: #6c757d;
        }

        .btn-primary {
            width: 100%;
            background-color: #007bff;
            border: none;
            padding: 10px;
            margin-top: 20px;
            font-size: 1.1em;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .profile-detail {
                flex-direction: column;
                text-align: left;
            }

            .profile-label, .profile-value {
                font-size: 1em;
            }

            .btn-primary {
                font-size: 1em;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container profile-container">
        <div class="profile-header">
            <h2>User Profile</h2>
        </div>
        <div class="card">
            <div class="profile-detail">
                <span class="profile-label">Name:</span>
                <span class="profile-value"><?php echo htmlspecialchars($user['name']); ?></span>
            </div>
            <div class="profile-detail">
                <span class="profile-label">Email:</span>
                <span class="profile-value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div class="profile-detail">
                <span class="profile-label">Phone:</span>
                <span class="profile-value"><?php echo htmlspecialchars($user['phone']); ?></span>
            </div>
            <div class="profile-detail">
                <span class="profile-label">Address:</span>
                <span class="profile-value"><?php echo htmlspecialchars($user['address']); ?></span>
            </div>
            <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>