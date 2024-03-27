
<?php
$authenticationSuccess = false;
$registrationSuccess = false;
$invalidUsername = false;
$invalidPassword = false;
$username = '';
$hashedPassword = '';

// Database connection parameters
$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$dbname = "shubham";

// Create connection
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedAction = $_POST['selectedAction'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate username for email format
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $invalidUsername = true;
    }

    // Apply SHA256 algorithm for password hashing
    $hashedPassword = hash('sha256', $password);
    echo $hashedPassword;

    if ($selectedAction === 'signUp') {
        // Check if the username already exists
        $checkUsernameQuery = "SELECT * FROM madhi WHERE username = '$username'";
        $result = $conn->query($checkUsernameQuery);

        if ($result->num_rows > 0) {
            $message = "Already signed up!";

           // JavaScript code to display an alert
            echo "<script>alert('$message');</script>";
            $registrationSuccess = false; // Already signed up
        } else {
            // Apply RSA algorithm on hashed password
            $hashedPassword = applyRSAlgorithm($hashedPassword);

            // Store the hashed password in the database
            $insertQuery = "INSERT INTO madhi (username, password) VALUES ('$username', '$hashedPassword')";
            if ($conn->query($insertQuery) === TRUE) {
                $registrationSuccess = true; // Sign up successful
            }

        }
    } elseif ($selectedAction === 'signIn') {
        // Apply RSA algorithm on hashed password
        $hashedPassword = applyRSAlgorithm($hashedPassword);

        // Check if the username and hashed password match in the database
        $checkUserQuery = "SELECT * FROM madhi WHERE username = '$username'";
        $result = $conn->query($checkUserQuery);

        if ($result->num_rows > 0) {
            // Check if the password matches
            $row = $result->fetch_assoc();
            if ($row['password'] === $hashedPassword) {
                $authenticationSuccess = true; // Sign in successful
            } else {
                $invalidPassword = true; // Invalid password
            }
        } else {
            $invalidUsername = true; // Invalid username
        }
    }
}

// Close the database connection
$conn->close();

function applyRSAlgorithm($hashedPassword) {
    //RSA LOGIC

    return $hashedPassword;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add Bootstrap CSS link here (if not already included) -->
    <link rel="stylesheet" href="bootstrap.css">
    <title>WELCOME</title>
    <style>
        body {
            background-color:peru;
        }

        .form-container {
            border: 5px solid black;
            padding: 20px;
            margin-top: 50px;
            text-align: center;
            background-color: palegoldenrod;
        }

        .warning {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 form-container">
            <h2 style="color:red"><i>WELCOME</h2>

            <form action="" method="post" onsubmit="return validateForm()">
                <label for="inputUsername"><b>Username (Email):</label>
                <input type="text" id="inputUsername" name="username" class="form-control" required>
                <br>

                <label for="inputPassword"><b>Password:</label>
                <div class="input-group"> <!-- Use Bootstrap's input-group class -->
                    <input type="password" id="inputPassword" name="password" class="form-control" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <input type="checkbox" onclick="showPassword()"> Show Password
                        </div>
                    </div>
                </div>
                <br>

                <label for="signIn">Sign In</label>
                <input type="radio" id="signIn" name="action" value="signIn" checked>

                <label for="signUp">Sign Up</label>
                <input type="radio" id="signUp" name="action" value="signUp">
                <br>

                <button type="submit" class="btn btn-primary">Submit</button>

                <?php
                if ($invalidUsername) {
                    echo '<div id="warningMessage" class="warning">you have not yet sign-up .</div>';
                }

                if ($invalidPassword) {
                    echo '<div id="warningMessage" class="warning">Invalid password.</div>';
                }

                if ($authenticationSuccess && $selectedAction === 'signIn') {
                    echo '<div id="successMessage" class="success"></div>';
                    echo '<script>alert("Sign In successful!"); window.location.href = "";</script>';
                }

                if ($registrationSuccess && $selectedAction === 'signUp') {
                    echo '<div id="successMessage" class="success"></div>';
                    echo '<script>alert("Sign up successful!"); window.location.href = "";</script>';
                    
                }


                
                ?>

                <div id="warningMessage" class="warning"></div>
                <input type="hidden" id="selectedAction" name="selectedAction" value="signIn">
            </form>
        </div>
    </div>
</div>

<script>
    function validateForm() {
    var username = document.getElementById("inputUsername").value;
    var password = document.getElementById("inputPassword").value;
    var warningMessage = document.getElementById("warningMessage");
    var selectedAction = document.querySelector('input[name="action"]:checked').value;

    if (!filterEmail(username)) {
        warningMessage.textContent = "Invalid username. Please enter a valid email address.";
        return false;
    } else if (username.trim() === "" || password.trim() === "") {
        warningMessage.textContent = "Username and password cannot be empty";
        return false;
    } else if (password.length < 8 || password.length > 16) {
        warningMessage.textContent = "Password must be between 8 and 16 characters";
        return false;
    } else {
        warningMessage.textContent = "";
        document.getElementById("selectedAction").value = selectedAction;
        return true;
    }
}


function filterEmail(email) {
    // Simple email validation using a regular expression
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}


    function showPassword() {
        var passwordInput = document.getElementById("inputPassword");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    }
</script>

<!-- Add Bootstrap JS and Popper.js scripts here (if needed) -->
<script src="popper.js"></script>
<script src="bootstrap.js"></script>
</body>
</html>
