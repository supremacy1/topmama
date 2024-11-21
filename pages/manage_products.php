<?php
// include('../includes/header.php');
include('../config/database.php');

// Fetch categories
try {
    $categoryStmt = $pdo->query("SELECT id, category_name FROM categories");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching categories: " . $e->getMessage());
}

// Fetch store locations
try {
    $locationStmt = $pdo->query("SELECT id, location_name FROM store_locations");
    $locations = $locationStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching locations: " . $e->getMessage());
}

// Handle product submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $normal_price = $_POST['normal_price'];
    $discounted_price = $_POST['discounted_price'] ?? NULL;
    $category_id = $_POST['category_id'];
    $location_id = $_POST['location_id'];

    // Insert product into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, normal_price, discounted_price, category_id, location_id) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $normal_price, $discounted_price, $category_id, $location_id]);
        $product_id = $pdo->lastInsertId();

        // Handle file uploads and store images in the database
        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
                if ($tmpName) {
                    $photoData = file_get_contents($tmpName);
                    $stmt = $pdo->prepare("INSERT INTO product_photos (product_id, photo_data) VALUES (?, ?)");
                    $stmt->bindParam(1, $product_id, PDO::PARAM_INT);
                    $stmt->bindParam(2, $photoData, PDO::PARAM_LOB);
                    $stmt->execute();
                }
            }
        }

        echo "<p class='alert alert-success'>Product added successfully!</p>";
    } catch (PDOException $e) {
        echo "<p class='alert alert-danger'>Error adding product: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        /* Sidebar styling */
        .sidebar {
            height: 100vh;
            width: 180px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
        }
        
        .sidebar h2 {
            text-align: center;
            font-weight: bold;
            color: #fff;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 15px;
            color: #ddd;
            display: block;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: #fff;
        }

        /* Content container adjusted for sidebar */
        .content-container {
            margin-left: 200px; /* Adjust according to sidebar width */
            padding: 20px;
            
        }

        .form-container {
            max-width: 800px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
           margin-top:10%;
        }

        .product-container {
            border: 1px solid #ccc;
            padding: 16px;
            margin: 16px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .product-details {
            flex: 1;
            padding: 0 16px;
        }

        .product-photos {
            display: flex;
            flex-direction: row;
            gap: 8px;
            overflow-x: auto;
        }

        .product-photos img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
    <title>Admin Dashboard</title>
</head>
<body>
<?php include 'sidebar.php'; ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <a href="dashboard">Dashbaord</a>
        <a href="manage_products">Manage Products</a>
        <a href="manage_orders">Manage Orders</a>
       
    </div>

    <!-- Content Container -->
    <div class="content-container">
        <!-- Existing form and product display content goes here -->
        <div class="form-container">
            <h2>Add Product</h2>

            <!-- HTML Form for Product Creation -->
            <form action="manage_products.php" method="POST" enctype="multipart/form-data">
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="name">Product Name:</label>
                        <input type="text" name="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label for="normal_price">Normal Price:</label>
                        <input type="number" name="normal_price" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label for="discounted_price">Discounted Price:</label>
                        <input type="number" name="discounted_price" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="category_id">Category:</label>
                        <select name="category_id" class="form-control form-control-sm" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="location_id">Location:</label>
                        <select name="location_id" class="form-control form-control-sm" required>
                            <option value="">Select Location</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?= htmlspecialchars($location['id']) ?>"><?= htmlspecialchars($location['location_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="photos">Upload Product Photos (Up to 5):</label>
                        <input type="file" name="photos[]" class="form-control-file form-control-sm" multiple>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea name="description" class="form-control form-control-sm" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>

        <!-- Display Products with Images -->
        <h2>Existing Products</h2>
        <?php
        // Fetch products with their category names and locations
        try {
            $stmt = $pdo->query("SELECT p.id, p.name, p.description, p.normal_price, p.discounted_price, 
                                        c.category_name AS category_name, l.location_name AS location_name 
                                 FROM products p 
                                 JOIN categories c ON p.category_id = c.id
                                 JOIN store_locations l ON p.location_id = l.id");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as $product) {
                echo "<div class='product-container'>";
                echo "<div class='product-details'>";
                echo "<p><strong>Name:</strong> " . htmlspecialchars($product['name']) . "</p>";
                echo "<p><strong>Category:</strong> " . htmlspecialchars($product['category_name']) . "</p>";
                echo "<p><strong>Location:</strong> " . htmlspecialchars($product['location_name']) . "</p>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($product['description']) . "</p>";
                echo "<p><strong>Price:</strong> $" . htmlspecialchars($product['normal_price']) . "</p>";
                if ($product['discounted_price']) {
                    echo "<p><strong>Discounted Price:</strong> $" . htmlspecialchars($product['discounted_price']) . "</p>";
                }
                echo "<a href='edit_product?id=" . htmlspecialchars($product['id']) . "' class='btn btn-secondary'>Edit Product</a>";
                echo "</div>";

                // Display product photos in a row
                echo "<div class='product-photos'>";
                $stmtPhotos = $pdo->prepare("SELECT id, photo_data FROM product_photos WHERE product_id = ?");
                $stmtPhotos->execute([$product['id']]);
                $photos = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);
                foreach ($photos as $photo) {
                    echo "<img src='data:image/jpeg;base64," . base64_encode($photo['photo_data']) . "' alt='Product Photo'>";
                }
                echo "</div>";
                echo "</div>";
            }
        } catch (PDOException $e) {
            echo "<p class='alert alert-danger'>Error fetching products: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

</body>
</html>
