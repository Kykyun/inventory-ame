<?php
include 'dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profileid'])) {
    $profileid = $_POST['profileid'];

    // Perform DELETE operation in the database
    $deleteQuery = "DELETE FROM profile WHERE profileid = $profileid";
    if (mysqli_query($conn, $deleteQuery)) {
        $response = "User deleted successfully";
    } else {
        $response = "Error deleting profile: " . mysqli_error($conn);
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
