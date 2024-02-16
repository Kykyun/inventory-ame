<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["profileid"])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Access the username from the session
$Profileid = $_SESSION["profileid"];
$profiletype= $_SESSION["profiletype"];
$username = $_SESSION["Username"];

include 'dbconnection.php';

// Check if a month, username, productname, or transaction type filter is set
$monthFilter = isset($_GET['monthFilter']) ? $_GET['monthFilter'] : '';
$usernameFilter = isset($_GET['usernameFilter']) ? $_GET['usernameFilter'] : '';
$productnameFilter = isset($_GET['productnameFilter']) ? $_GET['productnameFilter'] : '';
$transactionTypeFilter = isset($_GET['transactionTypeFilter']) ? $_GET['transactionTypeFilter'] : '';
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'desc'; // Default to descending order

// Modify the SQL query based on the selected filters and sorting order
$query = "SELECT t.transactionamount, t.transactiontype, t.transactiondate, p.productname, t.username 
          FROM transaction t
          INNER JOIN product p ON t.productid = p.productid";

// Check if any filters are selected
$filterApplied = false;

if (!empty($monthFilter)) {
    $query .= " AND MONTH(t.transactiondate) = '$monthFilter'";
    $filterApplied = true;
}

if (!empty($usernameFilter)) {
    $query .= " AND t.username LIKE '%$usernameFilter%'";
    $filterApplied = true;
}

if (!empty($productnameFilter)) {
    $query .= " AND p.productname LIKE '%$productnameFilter%'";
    $filterApplied = true;
}

if (!empty($transactionTypeFilter)) {
    $query .= " AND t.transactiontype LIKE '%$transactionTypeFilter%'";
    $filterApplied = true;
}

// If no filters are applied, remove the WHERE clause
if (!$filterApplied) {
    $query = str_replace("WHERE t.profileid = u.Profileid", "", $query);
}

// Add sorting order to the query
$query .= " ORDER BY t.transactiondate $sortOrder";

$result = mysqli_query($conn, $query);

// Check for errors in the query
if (!$result) {
    die('Error: ' . mysqli_error($conn));
}
// Get the current page file name
$current_page = basename($_SERVER['PHP_SELF']);

// Navigation items array or data structure
$nav_items = [
    ['link' => 'Outproduct.php', 'text' => 'Home', 'icon' => 'home-outline'],
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="Assets/css/dashboard.css">
    <style>


        main {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .container {
            max-width: 800px;
            margin-top: 30px;
        }

        label {
            margin-bottom: 8px;
            display: block;
        }

        select,
        input {
            width: calc(100% - 16px);
            padding: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button{
            background-color: #333;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            width: 100px;
            height: 53px;
            margin-bottom: 16px;
        }

        button:hover {
            background: white;
            color: #333;
        }

        /* Add this style for the print button */
        #printButton {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            width: 150px;
            margin-left: 10px;
        }

        #printButton:hover {
            background: #fff;
            color: #333;
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
                    <span class="header">Transaction History</span>
                </div>
        </div>
        <div class="container">
            <!-- Filter form above the table -->
            <form method="GET" action="">
                <label for="monthFilter">Filter by Month:</label>
                <select name="monthFilter" id="monthFilter">
                    <option value="">All Months</option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                <label for="usernameFilter">Filter by Username:</label>
                <input type="text" name="usernameFilter" id="usernameFilter" placeholder="Username"/>

                <label for="productnameFilter">Filter by Product Name:</label>
                <input type="text" name="productnameFilter" id="productnameFilter" placeholder="Product Name" />

                <label for="transactionTypeFilter">Filter by Transaction Type:</label>
                <select name="transactionTypeFilter" id="transactionTypeFilter">
                    <option value="">Transaction Type</option>
                    <option value="In">In</option>
                    <option value="Out">Out</option>
                </select>

                <!-- New filter for sorting order -->
                <label for="sortOrder">Sort Order Date/Time:</label>
                <select name="sortOrder" id="sortOrder">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>

                <button type="submit">Filter</button>
                <!-- Add this button after the filter button -->
                <button type="submit" id="printButton">Print to Excel</button>

            </form>

            <!-- Display the transaction history table -->
            <table>
                <tr>
                    <th>Product Name</th>
                    <th>Amount</th>
                    <th>Transaction Type</th>
                    <th>Date/Time</th>
                    <th>Username</th>
                </tr>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>' . $row['productname'] . '</td>';
                        echo '<td>' . $row['transactionamount'] . '</td>';
                        echo '<td>' . $row['transactiontype'] . '</td>';
                        echo '<td>' . $row['transactiondate'] . '</td>';
                        echo '<td>' . $row['username'] . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5">No history found.</td></tr>';
                }
                ?>
            </table>
        </div>
    </div>
    <!-- Add this script block after including other scripts -->
    <script>
        $(document).ready(function () {
            // Function to handle export to Excel
            function exportToExcel() {
                // Initialize CSV content with column headers and style for center alignment
                var csvContent = 'sep=,\n';
                csvContent += 'Product Name,Amount,Transaction Type,Date,Time,Username\n';

                // Add rows to the CSV content with data from the current table
                $('table tbody tr').each(function () {
                    $(this).find('td').each(function (index) {
                        // Format date and time in a way that Excel can recognize (if it's the Date/Time column)
                        if (index === 3) { // Assuming 3 is the index of the Date/Time column
                            var dateValue = new Date($(this).text());
                            var formattedDate = (dateValue.getMonth() + 1) + '/' + dateValue.getDate() + '/' + dateValue.getFullYear();
                            var formattedTime = dateValue.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                            csvContent += formattedDate + ',' + formattedTime + ',';
                        } else {
                            csvContent += $(this).text() + ',';
                        }
                    });
                    csvContent += '\n';
                });

                // Create a Blob from the CSV content
                var blob = new Blob([csvContent], {
                    type: 'text/csv;charset=utf-8;'
                });

                // Create a link element and trigger a click to download the file
                var a = document.createElement('a');
                a.href = window.URL.createObjectURL(blob);
                a.download = 'transaction_history.csv';
                a.click();
            }

            // Attach the export function to the print button click event
            $('#printButton').click(function () {
                exportToExcel();
            });
        });
    </script>






    <!-- =========== Scripts =========  -->
    <script src="Assets/js/dashboard.js"></script>
    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
