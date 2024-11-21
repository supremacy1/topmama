<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pizza & Chops</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <style>
       
        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .navbar {
            background-color: #007bff; 
        }
        .navbar-brand {
            color: #fff;
            font-weight: bold;
        }
        .navbar-nav .nav-link {
            color: #fff !important;
        }
        .navbar-nav .nav-link:hover {
            color: #d1ecf1 !important;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 200px;
            background-color: #2c3e50;
            padding-top: 20px;
            z-index: 1000;
        }
        .sidebar a {
            color: #ecf0f1;
            padding: 15px;
            text-decoration: none;
            display: block;
            font-weight: 500;
        }
        .sidebar a:hover {
            background-color: #34495e;
            color: #fff;
        }
        .sidebar .active {
            background-color: #34495e;
        }
        .content {
            margin-left: 200px;
            flex: 1;
            padding: 20px;
        }
        .sticky-navbar {
            position: fixed;
            width: calc(100% - 200px); 
            top: 0;
            left: 200px;
            z-index: 999;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column">
    <h4 class="text-white text-center">Admin Dashboard</h4>
    <a href="dashboard">Dashboard</a>
    <!-- <a href="manage_customers">Manage Customer</a> -->
    <a href="manage_products">Manage Products</a>
    <a href="manage_orders">Manage Orders</a>
   
</div>

<!-- Navigation Bar (Sticky) -->
<nav class="navbar navbar-expand-lg  navbar-dark sticky-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Pizza & Chops</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="offers.php">Our Offers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="stores.php">Our Stores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact Us</a>
                </li>
            </ul>
        </div>
    </div>
</nav>



<!-- Bootstrap JS (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
