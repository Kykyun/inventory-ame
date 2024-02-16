<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["profileid"])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['login_success_message'])) {
    echo "<script>alert('" . $_SESSION['login_success_message'] . "');</script>";
    unset($_SESSION['login_success_message']); // Remove the message after displaying it
}

// Access the username from the session
$Profileid = $_SESSION["profileid"];
$profiletype = $_SESSION["profiletype"];
$username = $_SESSION["Username"];

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
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="Assets/css/dashboard.css">
    <style>
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
                    <span class="header">Home</span>
                </div>
        </div>
        <div class="container">
        <table>
            <tr>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Balance</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
            <?php
            include 'dbconnection.php';

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $query = "SELECT * FROM product";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td><img src="' . $row['productimg'] . '" alt="' . $row['productname'] . '"></td>';
                    echo '<td>' . $row['productname'] . '</td>';
                    echo '<td>' . $row['productbalance'] . '</td>';
                    echo '<td><input type="text" id="amount_' . $row['productid'] . '" placeholder="Enter Amount"></td>';
                    echo '<td>';
                    echo '<button onclick="deductBalance(' . $row['productid'] . ')">Out</button>';
                    echo '<button class="delete-button" onclick="deleteProduct(' . $row['productid'] . ')"><ion-icon name="trash"></ion-icon></button>'; 
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5">No products found.</td></tr>';
            }

            $conn->close();
            ?>
        </table>
        </div>
    </div>
    

    <script>
        function deleteProduct(productId) {
            if (confirm("Are you sure you want to delete this product?")) {
                $.ajax({
                    url: 'deleteproduct.php',
                    method: 'POST',
                    data: { productId: productId },
                    success: function(response) {
                        location.reload();
                        alert(response);
                    }
                });
            }
        }

        function deductBalance(productId) {
            var amount = $('#amount_' + productId).val();

            $.ajax({
                url: 'deductbalance.php',
                method: 'POST',
                data: { productId: productId, amount: amount},
                success: function(response) {
                    location.reload();
                    alert(response);
                }
            });
        }
    </script>
    <!-- =========== Scripts =========  -->
    <script src="Assets/js/dashboard.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
