<?php
session_start();

// Database config
$host = "localhost";
$dbname = "medischedule";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get login form data
$email = $_POST['email'];
$password_input = $_POST['password'];  // User input password

// Query to find doctor by email
$sql = "SELECT * FROM doctors WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $doctor = $result->fetch_assoc();

    // Directly compare the entered password with the stored password
    if ($password_input === $doctor['password']) {
        // Start session and redirect
        $_SESSION['doctor'] = [
            'doctor_id'        => $doctor['doctor_id'],
            'first_name'       => $doctor['first_name'],
            'last_name'        => $doctor['last_name'],
            'email'            => $doctor['email'],
            'specialization'   => $doctor['specialization'],
            'phone_number'     => $doctor['phone_number'],
            'address'          => $doctor['address'],
            'experience_years' => $doctor['experience_years'],
        ];

        // Redirect to doctor home page
        header("Location: doctor-home.php");
        exit();
    } else {
        // Invalid password
        echo "<script>alert('Invalid password'); window.location.href='login-doctor.html';</script>";
    }
} else {
    // Doctor not found
    echo "<script>alert('Doctor not found with this email'); window.location.href='login-doctor.html';</script>";
}

$conn->close();
?>
