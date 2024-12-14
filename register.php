<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'login_system');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
   // $mo = trim($_POST['mo']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($username) || empty($password) || empty($confirm_password)) {
        header("Location: signup.php?message=All fields are required&type=error");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: signup.php?message=Passwords do not match&type=error");
        exit;
    }

    // Check if the username already exists
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: signup.php?message=Username already exists&type=error");
        exit;
    }

    // Hash the password for security
    $hashed_password = md5($password); // Use password_hash() for better security

    // Insert the user into the database
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        header("Location: signup.php?message=Signup successful! You can now login&type=success");
    } else {
        header("Location: signup.php?message=An error occurred. Please try again&type=error");
    }

    $stmt->close();
    $conn->close();
}
?>
