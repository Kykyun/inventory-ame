<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["profileid"])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit();
}

// Access the username from the session
$Profileid = $_SESSION["profileid"];
$profiletype= $_SESSION["profiletype"];
$username = $_SESSION["Username"];

// Database connection details
include "dbconnection.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $productname = $_POST['productname'];

    $sql = "SELECT productid, productbalance FROM product WHERE productname = '$productname'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $productid = $row['productid'];
        $productbalance = $row['productbalance'];

        $stockIn = $_POST['stockIn'];
        $rejectedStock = $_POST['rejectedStock'];
        $updatedbalance = $productbalance + ($stockIn - $rejectedStock);     

        // Now, insert the data into your stock table (assuming you have a 'stock' table)
        $insert_sql = "INSERT INTO stock (stockamount, stockreject, stockaccept, productbalance, productid,  username) VALUES ('$stockIn', '$rejectedStock', ('$stockIn' - '$rejectedStock'),'$updatedbalance', '$productid', '$username')";

        if ($conn->query($insert_sql) === TRUE) {

            $insert_query = "INSERT INTO `transaction`(`transactionamount`, `transactiontype`, `productid`, `username`) VALUES (('$stockIn' - '$rejectedStock'), 'In', '$productid', '$username')";
            if ($conn->query($insert_query) === TRUE) {
                
                $product_query = "UPDATE `product` SET `productbalance` = $updatedbalance WHERE `productid` = '$productid'";
                if ($conn->query($product_query) === TRUE) {
                    
                    echo "<script>alert('Balance added successfully');</script>";
                } else {
                    echo "<script>alert('Error updating product balance: '. $conn->error);</script>";
                }
            } else {
                echo "<script>alert('Error updating transaction record: ' . $conn->error);</script>";
            }
        } else {
            echo "<script>alert('Error: ' . $insert_sql . $conn->error);</script>";
        }
    } else {
        echo "<script>alert('Unable to insert to database');</script>";
    }
}
// Get the current page file name
$current_page = basename($_SERVER['PHP_SELF']);

// Navigation items array or data structure
$nav_items = [
    ['link' => 'outproduct.php', 'text' => 'Home', 'icon' => 'home-outline'],
    ['link' => 'addStock.php', 'text' => 'Add Stock', 'icon' => 'add-circle-outline'],
    ['link' => 'addproduct.php', 'text' => 'Add Product', 'icon' => 'add-outline'],
    ['link' => 'transactionhistory.php', 'text' => 'View Transaction History', 'icon' => 'list-outline'],
    ['link' => 'stockhistory.php', 'text' => 'View Stock History', 'icon' => 'list-circle-outline'],
    ['link' => 'usermanagement.php', 'text' => 'User Management', 'icon' => 'person-circle-outline'],
    ['link' => 'logout.php', 'text' => 'Sign Out', 'icon' => 'log-out-outline'],
];

// Include 'User Management' only for Admin users
if ($profiletype !== 'Admin') {
    // If the user is not an Admin, remove 'User Management' from the array
    $nav_items = array_filter($nav_items, function ($item) {
        return $item['link'] !== 'usermanagement.php';
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="Assets/css/dashboard.css">
    <style>
        .card {
            max-width: 500px;
            width: 100%;
            background-color: #fff;
            padding: 20px;
            border-radius: 20px;
            border: 5px solid #FFB689;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        label {
            margin-bottom: 8px;
            display: block;
        }

        select, input {
            width: calc(100% - 16px);
            padding: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Responsive Styles for Mobile View */
        @media (max-width: 767px) {
            .container {
                margin-top: 10px; /* Adjust margin for better spacing on smaller screens */
                overflow-x: auto; /* Enable horizontal scrolling for the container */
            }

            table {
                font-size: 12px; /* Adjust font size for better readability on smaller screens */
                width: 100%; /* Make the table full width */
                white-space: nowrap; /* Prevent text wrapping */
            }

            th, td {
                padding: 8px;
            }

            .navigation.active + .main .topbar,
            .navigation.active + .main .toggle {
                display: none; /* Hide header and menu icon when navigation is active */
            }

            .topbar {
                font-size: 1.5rem; /* Adjust the font size for the topbar in mobile view */
                height: 40px; /* Adjust the height of the topbar in mobile view */
            }
        }

    </style>
</head>
    
<div class="navigation">
        <ul>
                <li>
                    <a href="#">
                        <span class="logo">
                            <img class="logo" src="Assets/Logo.png" alt="Logo">
                        </span>
                        <span class="title">INVENTORY SYSTEM</span>
                    </a>
                </li>
                <?php
                    foreach ($nav_items as $nav_item) {
                        $class = ($current_page === $nav_item['link']) ? 'clicked' : '';
                        echo '<li class="' . $class . '">';
                        echo '<a href="' . $nav_item['link'] . '">';
                        echo '<span class="icon"><ion-icon name="' . $nav_item['icon'] . '"></ion-icon></span>';
                        echo '<span class="title">' . $nav_item['text'] . '</span>';
                        echo '</a>';
                        echo '</li>';
                    }
                ?>                              
        </ul>       
    </div>

    <div class="main">
        <div class="topbar">
                    <div class="toggle">
                        <span class="icon">
                        <ion-icon name="menu-outline"></ion-icon>
                        </span>
                        <span class="header">Add Stock</span>
                    </div>
            </div>                      
        <div class="container">
            <div class="card">
                    <form id="stockForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <label for="productname">Product Name:</label>
                        <select id="productname" name="productname" required>
                            <?php
                            // Fetch product names from the database
                            $sql = "SELECT * FROM product";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['productname'] . '" data-image="' . $row['productimg'] . '">' . $row['productname'] .  '</option>';
                                }
                            }
                            ?>
                        </select><br>

                        <img id="productImage" src="" alt=""><br>

                        <label for="stockIn">Stock In:</label>
                        <input type="number" id="stockIn" name="stockIn" required><br>

                        <label for="rejectedStock">Rejected Stock:</label>
                        <input type="number" id="rejectedStock" name="rejectedStock" required><br>

                        <label for="stockBalance">Total Stock:</label>
                        <input type="number" id="stockBalance" name="stockBalance" readonly><br>

                        <input type="submit" name="submit" value="Submit">
                    </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Calculate total stock
            const stockInInput = document.getElementById('stockIn');
            const rejectedStockInput = document.getElementById('rejectedStock');
            const stockBalanceInput = document.getElementById('stockBalance');

            stockInInput.addEventListener('input', calculateTotalStock);
            rejectedStockInput.addEventListener('input', calculateTotalStock);

            function calculateTotalStock() {
                const stockIn = stockInInput.value;
                const rejectedStock = rejectedStockInput.value;
                const stockBalance = stockIn - rejectedStock;
                stockBalanceInput.value = stockBalance;
            }

            const productImage = document.getElementById('productImage');
            const productSelect = document.getElementById('productname');

            // Function to update the image when a product is selected
            function updateProductImage() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const imagePath = selectedOption.getAttribute('data-image');
                productImage.src = imagePath;
            }

            // Initial image update on page load
            updateProductImage();

            // Attach the update function to the select input's change event
            productSelect.addEventListener('change', updateProductImage);
        });
    </script>
    <script src="Assets/js/dashboard.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>
</html>

