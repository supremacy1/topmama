<?php
include('../includes/header.php');
include('../config/database.php');

if (!isset($_GET['id'])) {
    die("Product ID not specified.");
}

$product_id = $_GET['id'];

// Fetch the product data
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Product not found.");
    }
} catch (PDOException $e) {
    die("Error fetching product: " . $e->getMessage());
}

// Fetch categories and locations
try {
    $categoryStmt = $pdo->query("SELECT id, category_name FROM categories");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

    $locationStmt = $pdo->query("SELECT id, location_name FROM store_locations");
    $locations = $locationStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching categories or locations: " . $e->getMessage());
}

// Handle product update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $normal_price = $_POST['normal_price'];
    $discounted_price = $_POST['discounted_price'] ?? NULL;
    $category_id = $_POST['category_id'];
    $location_id = $_POST['location_id'];

    // Update product in the database
    try {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, normal_price = ?, 
                                discounted_price = ?, category_id = ?, location_id = ? 
                                WHERE id = ?");
        $stmt->execute([$name, $description, $normal_price, $discounted_price, $category_id, $location_id, $product_id]);

        echo "<p class='alert alert-success'>Product updated successfully!</p>";
    } catch (PDOException $e) {
        echo "<p class='alert alert-danger'>Error updating product: " . $e->getMessage() . "</p>";
    }
}

// Handle image deletion
if (isset($_GET['delete_image'])) {
    $photo_id = $_GET['delete_image'];

    try {
        $deleteStmt = $pdo->prepare("DELETE FROM product_photos WHERE id = ?");
        $deleteStmt->execute([$photo_id]);
        echo "<p class='alert alert-success'>Image deleted successfully!</p>";
    } catch (PDOException $e) {
        echo "<p class='alert alert-danger'>Error deleting image: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Sidebar styling */
        .sidebar {
            height: 100vh;
            width: 250px;
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
            font-size: 18px;
            color: #ddd;
            display: block;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: #fff;
        }

        /* Content container adjusted for sidebar */
        .content-container {
            margin-left: 270px; /* Adjust according to sidebar width */
            padding: 20px;
        }

        .form-container {
            max-width: 800px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .photo-container {
            display: inline-block;
            margin: 10px;
        }

        .photo-container img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }

        .photo-container .button {
            display: block;
            margin-top: 5px;
            text-align: center;
        }
    </style>
    <title>Edit Product</title>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <a href="manage_products">Manage Products</a>
        <a href="manage_orders">Manage Orders</a>
        <a href="manage_customers">Manage Customers</a>
        <!-- <a href="reports">Reports</a>
        <a href="settings">Settings</a> -->
    </div>

    <!-- Content Container -->
    <div class="content-container">
        <h2>Edit Product</h2>

        <!-- HTML Form for Editing Product -->
        <div class="form-container">
            <form action="edit_product.php?id=<?= htmlspecialchars($product_id) ?>" method="POST">
                <label for="name">Product Name:</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>

                <label for="description">Description:</label>
                <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea><br>

                <label for="normal_price">Normal Price:</label>
                <input type="number" name="normal_price" value="<?= htmlspecialchars($product['normal_price']) ?>" required><br>

                <label for="discounted_price">Discounted Price:</label>
                <input type="number" name="discounted_price" value="<?= htmlspecialchars($product['discounted_price']) ?>"><br>

                <!-- Category Dropdown -->
                <label for="category_id">Category:</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']) ?>" <?= ($category['id'] == $product['category_id']) ? 'selected' : '' ?>><?= htmlspecialchars($category['category_name']) ?></option>
                    <?php endforeach; ?>
                </select><br>

                <!-- Location Dropdown -->
                <label for="location_id">Location:</label>
                <select name="location_id" required>
                    <option value="">Select Location</option>
                    <?php foreach ($locations as $location): ?>
                        <option value="<?= htmlspecialchars($location['id']) ?>" <?= ($location['id'] == $product['location_id']) ? 'selected' : '' ?>><?= htmlspecialchars($location['location_name']) ?></option>
                    <?php endforeach; ?>
                </select><br>

                <button type="submit">Update Product</button>
            </form>
        </div>

        <h2>Existing Product Images</h2>
        <?php
        // Fetch existing images
        try {
            $stmtPhotos = $pdo->prepare("SELECT id, photo_data FROM product_photos WHERE product_id = ?");
            $stmtPhotos->execute([$product_id]);
            $photos = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);

            foreach ($photos as $photo) {
                echo "<div class='photo-container'>";
                echo "<img src='data:image/jpeg;base64," . base64_encode($photo['photo_data']) . "' alt='Product Image' />";
                echo "<a href='edit_product.php?id=" . htmlspecialchars($product_id) . "&delete_image=" . htmlspecialchars($photo['id']) . "' class='button'>Delete Image</a>";
                echo "</div>";
            }
        } catch (PDOException $e) {
            echo "<p class='alert alert-danger'>Error fetching product images: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

</body>
</html>
