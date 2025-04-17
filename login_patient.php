<?php
session_start();

// Database config
$host = "localhost";
$dbname = "medischedule";
$username = "root";
$password = "";

// Create DB connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get input
$email = $_POST['email'];
$password = $_POST['password'];

// Query to find patient by email
$sql = "SELECT * FROM patients WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Store user info in session
        $_SESSION['patient'] = [
            'patient_id'    => $user['patient_id'],
            'first_name'    => $user['first_name'],
            'last_name'     => $user['last_name'],
            'email'         => $user['email'],
            'dob'           => $user['dob'],
            'gender'        => $user['gender'],
            'phone_number'  => $user['phone_number'],
            'address'       => $user['address']
        ];

        // Redirect to dashboard or homepage
        header("Location: home-patient.php");
        exit();
    } else {
        echo "<script>alert('Invalid password'); window.location.href='login-patient.html';</script>";
    }
} else {
    echo "<script>alert('Email not found'); window.location.href='login-patient.html';</script>";
}

$conn->close();
?>
