<?php
// Include the database connection file
include 'db_connection.php';

// Fetch products and associated images
$productQuery = "SELECT p.id, p.name, p.description, p.normal_price, p.discounted_price, pp.photo_url, pp.photo_data 
                 FROM products p
                 LEFT JOIN product_photos pp ON p.id = pp.product_id
                 ORDER BY p.id, pp.id";
$productResult = $conn->query($productQuery);

// Fetch store locations and fees
$storeQuery = "SELECT id, location_name, delivery_fee FROM store_locations";
$storeResult = $conn->query($storeQuery);

// Store locations in an array
$stores = [];
if ($storeResult->num_rows > 0) {
    while ($storeRow = $storeResult->fetch_assoc()) {
        $stores[] = $storeRow;
    }
}

// Initialize variables to group images by product
$products = [];

if ($productResult->num_rows > 0) {
    while ($row = $productResult->fetch_assoc()) {
        $product_id = $row['id'];
        
        // Add the product details if not already in the array
        if (!isset($products[$product_id])) {
            $products[$product_id] = [
                'name' => $row['name'],
                'description' => $row['description'],
                'normal_price' => $row['normal_price'],
                'discounted_price' => $row['discounted_price'],
                'images' => []
            ];
        }

        // Add image URL or blob data to images array
        if (!empty($row['photo_url'])) {
            $products[$product_id]['images'][] = $row['photo_url'];
        } elseif (!empty($row['photo_data'])) {
            $base64_image = 'data:image/jpeg;base64,' . base64_encode($row['photo_data']);
            $products[$product_id]['images'][] = $base64_image;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .modal-content {
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
            transform: translateY(-20px);
            opacity: 0;
        }
        .modal.show .modal-content {
            transform: translateY(0);
            opacity: 1;
        }
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .product-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            
           
           
        }
        .product-card {
            width: 300px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* border-top: 10px solid blue; */
           
        }
        .product-card img {
            width: 100%;
            
        }
        .product-details {
            padding: 15px;
            text-align: center;
          
        border-top: 1px solid skyblue; 

        }
        .order-button {
            margin-top: 3px;
        }
        /* .container{
            width: 300px;
            max-height:20px;
        } */
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="product-gallery  ">
        <?php foreach ($products as $product_id => $product) : ?>
            <div class="product-card ">
                <div class="image-container  " >
                    <!-- Display the first image as the main large image -->
                    <img class="main-image " src="<?= htmlspecialchars($product['images'][0]) ?>" alt="Main Product Image" onclick="openPreviewModal(this)">

                    <!-- Display smaller images below -->
                    <div class="thumbnail-container mt-2 d-flex justify-content-center ">
                        <?php foreach ($product['images'] as $image): ?>
                            <img class="thumbnail mr-1" src="<?= htmlspecialchars($image) ?>" alt="Thumbnail Image" style="width: 50px;" onclick="setMainImage(this, this.closest('.product-card'))">
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="product-details">
                    <h2><?= htmlspecialchars($product['name']) ?></h2>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p>Price: $<?= htmlspecialchars($product['normal_price']) ?></p>
                    <button class="btn btn-primary order-button" onclick="openOrderForm(<?= $product_id ?>, '<?= htmlspecialchars($product['name']) ?>', <?= htmlspecialchars($product['normal_price']) ?>)">Order Now</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<!-- Modal for Order Form -->
<div id="orderModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h5 class="modal-title">Order Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="orderForm" class="p-3">
                <input type="hidden" name="product_id" id="product_id">
                <input type="hidden" name="price" id="price">
                
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control form-control-sm" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control form-control-sm" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" class="form-control form-control-sm" name="phone" id="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" class="form-control form-control-sm" name="address" id="address" required>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" class="form-control form-control-sm" name="quantity" id="quantity" min="1" required>
                </div>
                <div class="form-group">
                    <label for="location">Store Location:</label>
                    <select name="location_id" id="location" class="form-control form-control-sm" onchange="updateDeliveryFee()">
                        <?php foreach ($stores as $store): ?>
                            <option value="<?= $store['id'] ?>" data-fee="<?= $store['delivery_fee'] ?>">
                                <?= htmlspecialchars($store['location_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <p>Delivery Fee: $<span id="delivery-fee">0.00</span></p>
                </div>
                <button type="submit" class="btn btn-success btn-block">Submit Order</button>
            </form>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div id="successModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-body text-center">
                <h5>Order placed successfully!</h5>
                <button type="button" class="btn btn-primary mt-3" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Image Preview -->
<div id="previewModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 10px; right: 15px;">
                <span aria-hidden="true">&times;</span>
            </button>
            <img id="previewImage" src="" alt="Preview Image" style="width: 100%;">
        </div>
    </div>
</div>


<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>	
<script>
    function openOrderForm(productId, productName, price) {
        document.getElementById("product_id").value = productId;
        document.getElementById("price").value = price;
        $('#orderModal').modal('show');
    }

    function closeOrderModal() {
        $('#orderModal').modal('hide');
    }

    function updateDeliveryFee() {
        const locationSelect = document.getElementById("location");
        const selectedOption = locationSelect.options[locationSelect.selectedIndex];
        const fee = selectedOption.getAttribute("data-fee");
        document.getElementById("delivery-fee").textContent = parseFloat(fee).toFixed(2);
    }

    function openPreviewModal(imageElement) {
        const previewImage = document.getElementById('previewImage');
        previewImage.src = imageElement.src;
        $('#previewModal').modal('show');
    }

    function setMainImage(thumbnail, productCard) {
        const mainImage = productCard.querySelector('.main-image');
        mainImage.src = thumbnail.src;
    }
    function openOrderForm(productId, productName, price) {
    document.getElementById("product_id").value = productId;
    document.getElementById("price").value = price;
    $('#orderModal').modal('show');
}

function updateDeliveryFee() {
    const locationSelect = document.getElementById("location");
    const selectedOption = locationSelect.options[locationSelect.selectedIndex];
    const fee = selectedOption.getAttribute("data-fee");
    document.getElementById("delivery-fee").textContent = parseFloat(fee).toFixed(2);
}

// Handle form submission using AJAX
document.getElementById("orderForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Collect form data
    const formData = new FormData(this);

    // Send AJAX request
    fetch('process_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data); // For debugging

        // Close the order form modal
        $('#orderModal').modal('hide');

        // Show success message
        $('#successModal').modal('show');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while placing your order. Please try again.');
    });
});

// Initialize delivery fee on page load
document.addEventListener("DOMContentLoaded", updateDeliveryFee);

    document.addEventListener("DOMContentLoaded", updateDeliveryFee);
</script>

</body>
</html>
