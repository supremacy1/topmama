<?php include('../includes/header.php'); ?>
<link href="css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-dark sidebar">
            <div class="sidebar-sticky">
                <h4 class="text-white p-3">Admin Dashboard</h4>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link text-white" href="manage_products">Manage Products</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="manage_orders">Manage Orders</a></li>
                    <!-- <li class="nav-item"><a class="nav-link text-white" href="manage_customers">Manage Customers</a></li> -->
                    <!-- <li class="nav-item"><a class="nav-link text-white" href="reports">Reports</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="settings">Settings</a></li> -->
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main role="main" class="col-md-10 ml-sm-auto px-4">
            <div class="d-flex justify-content-between height-100vh flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2>Dashboard</h2>
            </div>
             <?php include('../pages/manage_customers.php'); ?> 
            <!-- Dashboard Cards -->
            <!-- <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Orders</h5>
                            <p class="card-text">40,876</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Sales</h5>
                            <p class="card-text">38,876</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Profit</h5>
                            <p class="card-text">$12,876</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Returns</h5>
                            <p class="card-text">11,086</p>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Recent Sales and Top Selling Product -->
            <!-- <div class="row">
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            Recent Sales
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>27 Jan 2021</td>
                                        <td>David Warner</td>
                                        <td>Delivered</td>
                                        <td>$250</td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> -->

                <!-- Top Selling Products -->
                <!-- <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            Top Selling Product
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Product A <span class="badge badge-primary badge-pill">220</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div> -->
        </main>
    </div>
</div>


