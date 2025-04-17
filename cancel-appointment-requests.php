<?php
session_start();

// Check if doctor session is set
if (!isset($_SESSION['doctor'])) {
    header("Location: login-doctor.html");
    exit;
}

$doctor = $_SESSION['doctor'];
$doctor_id = $doctor['doctor_id'];

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

// Handle Appointment Cancellation
if (isset($_POST['cancel_appointment'])) {
    $appointment_id = $_POST['appointment_id'];

    // SQL to delete the appointment
    $sql_cancel = "DELETE FROM appointments WHERE appointment_id = ? AND doctor_id = ?";
    $stmt_cancel = $conn->prepare($sql_cancel);
    $stmt_cancel->bind_param("ii", $appointment_id, $doctor_id);
    $stmt_cancel->execute();

    if ($stmt_cancel->affected_rows > 0) {
        echo "<script>alert('Appointment canceled successfully!');</script>";
    } else {
        echo "<script>alert('Failed to cancel appointment. Please try again later.');</script>";
    }
}

// Fetch Upcoming Appointments without checking for 'status'
$sql_upcoming = "SELECT a.appointment_id, a.appointment_date, p.first_name AS patient_first_name, p.last_name AS patient_last_name
                 FROM appointments a
                 JOIN patients p ON a.patient_id = p.patient_id
                 WHERE a.doctor_id = ? AND a.appointment_date > NOW()
                 ORDER BY a.appointment_date ASC";
$stmt_upcoming = $conn->prepare($sql_upcoming);
$stmt_upcoming->bind_param("i", $doctor_id);
$stmt_upcoming->execute();
$result_upcoming = $stmt_upcoming->get_result();
$upcoming_appointments = [];
while ($row = $result_upcoming->fetch_assoc()) {
    $upcoming_appointments[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cancel Appointment Requests - MediSchedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body  style="background: url('background.jpg') no-repeat center center fixed; background-size: cover;">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand text-danger fw-bold" href="#">
                <img src="logo.png" alt="MediSchedule Logo" height="40" class="me-2" />
                MediSchedule
            </a>
            <div class="ms-auto d-flex align-items-center">
                <span class="btn btn-outline-danger"><?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></span>
                <!-- Logout Button -->
                <a href="logout.php" class="btn btn-outline-danger ms-3">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container-fluid my-5 px-4">
        <div class="p-4 bg-white rounded shadow" style="opacity: 0.96">
            <h3 class="text-center text-danger mb-4">Upcoming Appointments - Cancel Requests</h3>

            <!-- Appointments List -->
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="p-4 border rounded bg-light">
                        <h5 class="text-center mb-3">Upcoming Appointments</h5>
                        <ul class="list-group">
                            <?php if (empty($upcoming_appointments)): ?>
                                <li class="list-group-item text-center">No upcoming appointments.</li>
                            <?php else: ?>
                                <?php foreach ($upcoming_appointments as $appointment): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <?= $appointment['appointment_date'] ?> - Patient: <?= $appointment['patient_first_name'] . ' ' . $appointment['patient_last_name'] ?>
                                        </div>
                                        <!-- Cancel Button -->
                                        <form action="cancel-appointment-requests.php" method="POST" class="mb-0">
                                            <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>" />
                                            <button type="submit" name="cancel_appointment" class="btn btn-danger btn-sm">Cancel</button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Go Back to Doctor Home Button -->
            <div class="row justify-content-center text-center g-4 mt-4">
                <div class="col-md-4">
                    <a href="doctor-home.php" class="btn btn-outline-danger w-100 py-2">Go Back to Doctor Home</a>
                    <small class="text-muted d-block mt-1">Return to your doctor home page.</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
