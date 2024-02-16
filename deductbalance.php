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
$username= $_SESSION["Username"];


// Function to retrieve product balance based on product id
function getProductBalance($conn, $productId)
{
    $productId = filter_var($productId, FILTER_SANITIZE_NUMBER_INT);

    $selectQuery = "SELECT productbalance FROM product WHERE productid = $productId";
    $result = $conn->query($selectQuery);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['productbalance'];
    } else {
        return false; // Product not found
    }
}

// Retrieve the productId and amount from the POST request
if (isset($_POST['productId']) && isset($_POST['amount'])) {
    $productId = $_POST['productId'];
    $amount = $_POST['amount'];

    // Sanitize and validate the input (you should perform more validation as needed)
    $productId = filter_var($productId, FILTER_SANITIZE_NUMBER_INT);
    $amount = filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Connect to your MySQL database here
    include 'dbconnection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the current product balance
    $currentBalance = getProductBalance($conn, $productId);

    if ($currentBalance !== false) {
        // Query to deduct the balance for the specified product
        $updateQuery = "UPDATE product SET productbalance = ($currentBalance - $amount) WHERE productid = $productId";

        $insertQuery = "INSERT INTO `transaction`(`transactionamount`, `transactiontype`, `productid`, `username`) VALUES ('$amount', 'Out', '$productId', '$username')";

        if ($conn->query($updateQuery) === TRUE && $conn->query($insertQuery) === TRUE) {
            echo "Balance deducted successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Product not found";
    }

    // Close your MySQL connection here
    $conn->close();
} else {
    echo "Invalid parameters";
}
?>
