<?php

include 'dbconnection.php';

// Add the getProfileId function here
function getProfileId($conn, $username) {
    $username = mysqli_real_escape_string($conn, $username); // Sanitize input

    $sql = "SELECT profileid FROM profile WHERE Username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        return $row['profileid'];
    } else {
        return null; // User not found or multiple users with the same username (which shouldn't happen)
    }
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["login"])) {
        // Handle login
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Validate the login credentials
        $sql = "SELECT * FROM profile WHERE Username = '$username' AND Password = '$password'";
        $result = $conn->query($sql);

        if (mysqli_num_rows($result) == 1) {
            // Login successful
            $row = $result->fetch_assoc();
            $profileid = $row['profileid'];
            $profiletype = $row['profiletype'];

            session_start();
            $_SESSION['logged_in'] = true;
            $_SESSION['profileid'] = $profileid;
            $_SESSION['profiletype'] = $profiletype;
            $_SESSION['Username'] = $username;

            header('Location: outproduct.php');

            $_SESSION['login_success_message'] = "Logged in successfully!";
            exit;
        } else {
            // Login failed
            echo "<script>alert('Login failed'); window.location.href='index.php';</script>";
            exit;
        }
    } else if (isset($_POST["register"])) {
        // Handle registration
        $regUsername = $_POST["regUsername"];
        $regEmail = $_POST["regEmail"];
        $regPassword = $_POST["regPassword"];
        
        // Validate if username and email are not already used
        $checkDuplicate = "SELECT * FROM profile WHERE Username='$regUsername' OR Email='$regEmail'";
        $result = $conn->query($checkDuplicate);

        if ($result->num_rows > 0) {
            echo "<script>alert('Username or email already in use');</script>";
        } else {
            // Insert new user into the database
            $insertUser = "INSERT INTO profile (Username, Email, Password, profiletype) VALUES ('$regUsername', '$regEmail', '$regPassword', 'User')";
            if ($conn->query($insertUser) === TRUE) {
                echo "<script>alert('Registration successful');</script>";
            } else {
                echo "<script>alert('Error: " . $conn->error . "');</script>";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(45deg, #f7dbcf, #f0b19a, #f59c71, #ee7148, #cc826b);
            background-size: 300% 300%;
            animation: color 12s ease-in-out infinite;
        }

        @keyframes color{
            0%{
                background-position: 0 50%;
            }

            50%{
                background-position: 100% 50%;
            }

            100%{
                background-position: 0 50%;
            }
        }

        .container {
            width: 500px;
            text-align: center;
        }

        form {
            width: 100%; /* Set the width of the form to 100% */
            max-width: 400px; /* Set the maximum width of the form */
            margin: 0 auto; /* Center the form horizontally */
            display: flex;
            flex-direction: column;
            background-color: #fff;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #FFB689; /* Change the stroke color here */
            margin-top: 20px;
        }

        label {
            margin-bottom: 8px;
        }

        input {
            padding: 8px;
            margin-bottom: 16px;
        }

        button {
            padding: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        /* Style for the "Register" button */
        button[name="register"] {
            background-color: #7A9598; /* Silver color */
            color: #333;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }

        /* Style for the "Login" button */
        button[name="login"] {
            background-color: #FFD700; /* Gold color */
            color: #333;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            margin-top: 8px; /* Add a gap between the two buttons */
        }

        input[type="submit"] {
            padding: 10px;
            border: 2px solid #333;
            border-radius: 5px;
            cursor: pointer;
            background-color: #CB40C7;
            color: white;
            font-weight: bold;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #FFA2FC;
            color: #333;
        }

        button:hover {
            background: black;
            color: #fff;
        }

        #registerForm {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            color: #333;
            cursor: pointer;
        }

        ul li a[href="logout.php"] {
            color: red;
        }

        .logo {
            width: 100px; /* Set the width of the logo */
            height: auto; /* Maintain aspect ratio */
            margin: 100 auto 10px auto; /* Center the logo horizontally and add space below it */
            background-color: #FFFFFF; /* Background color */
            padding: 5px; /* Padding for background color */
            border-radius: 20px; /* Border radius for background color */
            border: 2px solid #ff5349; /* Stroke color for the logo */
        }

        .logo-text {
            margin-top: 10px;
            font-family: 'Arial', sans-serif; /* Change the font family */
            font-weight: bold; /* Make the text bold */
            font-size: 25px;
            animation: fade 12s ease-in-out infinite;
        }

        @keyframes fade {
            0%{
                color: black;
            }

            50%{
                color: white;
            }

            100%{
                color: black;
            }
        }

        input[type="text"] {
            padding: 10px; /* Adjust the padding as needed */
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            transition: border-color 0.3s;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="text"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        input[type="password"] {
            padding: 10px; /* Adjust the padding as needed */
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            transition: border-color 0.3s;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        input[type="email"] {
            padding: 10px; /* Adjust the padding as needed */
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            transition: border-color 0.3s;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="email"]:focus {
            border-color: #4CAF50;
            outline: none;
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

<body>
    
    <div class="container">
    <div class="logo-container">
            <img class="logo" src="Assets/Logo.png" alt="Logo">
            <p class="logo-text">INVENTORY MANAGEMENT SYSTEM</p>
        </div>
        <!-- Your login form here -->
        <div id="loginForm">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <button onclick="openForm()">Register</button>
            </form>
        </div>
    </div>

    <!-- Your registration form here -->
    <div id="registerForm" style="display: none;">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <span onclick="closeForm()" style="cursor: pointer; float: right;">&times;</span>
            <input type="text" name="regUsername" placeholder="Username" required>
            <input type="email" name="regEmail" placeholder="Email" required>
            <input type="password" name="regPassword" placeholder="Password" required>
            <button type="submit" name="register">Register</button>
        </form>
    </div>

    <script>
        function openForm() {
            document.getElementById("registerForm").style.display = "flex";
        }

        function closeForm() {
            document.getElementById("registerForm").style.display = "none";
        }

        // Close the registration form if the user clicks outside the form
        window.onclick = function (event) {
            var registerForm = document.getElementById("registerForm");
            if (event.target === registerForm) {
                registerForm.style.display = "none";
            }
        }


    </script>

</body>

</html>
