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

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection parameters
    include 'dbconnection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Process user inputs
    $product_name = $_POST["product_name"];
    $product_stock = $_POST["product_stock"];

    // Check if the product name already exists
    $check_duplicate_sql = "SELECT COUNT(*) FROM product WHERE productname = ?";
    $check_stmt = $conn->prepare($check_duplicate_sql);
    $check_stmt->bind_param("s", $product_name);
    $check_stmt->execute();
    $check_stmt->bind_result($existing_count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($existing_count > 0) {
        // Product name already exists, show an error message
        $error_message = "Product with this name already exists. Please choose a different name.";
    } else {
        // Check the size of the uploaded image
        $max_image_size = 3 * 1024 * 1024; // 10MB in bytes
        if ($_FILES["product_image"]["size"] > $max_image_size) {
            // Image size exceeds the limit, show an error message
            $error_message = "Error: Image size exceeds the limit of 3MB. Please choose a smaller image size.";
        } else {
            // Handle the uploaded image
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["product_image"]["name"]);

            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                // Image uploaded successfully, now insert data into the database
                $sql = "INSERT INTO product (productname, productstock, productbalance, productimg) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("siis", $product_name, $product_stock, $product_stock, $target_file);

                if ($stmt->execute()) {
                    // Product added successfully
                    $error_message = "Product Added Successfully";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
                $stmt->close();
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Close the database connection
    $conn->close();
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
    <title>Add Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="Assets/css/dashboard.css">
    <style>
        h1 {
            color: #FFFFFF;
            text-align: center;
        }

        nav {
            background-color: #f1f1f1;
            overflow: hidden;
        }

        nav a {
            float: left;
            display: block;
            color: #333;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        nav a:hover {
            background-color: #ddd;
        }

        main {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

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
<body>
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
                <span class="header">Add Product</span>
                </div>
            </div>
        <div class="container">
            <div class="card">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                        <label for="product_name">Product Name:</label>
                        <input type="text" id="product_name" name="product_name" required>
                        
                        <label for="product_image">Product Image: (Max: 3MB)</label>
                        <input type="file" id="product_image" name="product_image" accept="image/*" required>
                        
                        <label for="product_stock">Stock Amount:</label>
                        <input type="number" id="product_stock" name="product_stock" required>
                        
                        <input type="submit" value="Submit">
                    </form>
            </div>
        </div>
    </div>
    <!-- =========== Scripts =========  -->
    <script>
        var errorMessage = "<?php echo $error_message; ?>";

        if (errorMessage !== "") {
            alert(errorMessage);
        }
    </script>
    <script src="Assets/js/dashboard.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>