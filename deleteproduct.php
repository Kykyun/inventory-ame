<?php
include 'dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productId'])) {
    $productId = $_POST['productId'];

    // Perform DELETE operation in the database
    $deleteQuery = "DELETE FROM product WHERE productid = $productId";
    if (mysqli_query($conn, $deleteQuery)) {
        $response = "Product deleted successfully";
    } else {
        $response = "Error deleting product: " . mysqli_error($conn);
    }

    // Close the database connection
    $conn->close();

    // Return the response
    echo $response;
} else {
    // If the request method is not POST or productId is not set, return an error response
    echo "Invalid request";
}
?>
