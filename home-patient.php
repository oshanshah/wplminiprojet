<?php
session_start();
if (!isset($_SESSION['patient'])) {
    header('Location: login.php');
    exit();
}

$patientId = $_SESSION['patient'];
$servername = "localhost";
$username = "root";
$password = "";
$database = "medischedule";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Total Appointments
$totalAppointments = 0;
$sql = "SELECT COUNT(*) as total FROM appointments";
$stmt = $conn->prepare($sql);
$stmt->execute();
$stmt->bind_result($totalAppointments);
$stmt->fetch();
$stmt->close();

// Total Doctors
$totalDoctors = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM doctors");
if ($row = $result->fetch_assoc()) {
    $totalDoctors = $row['total'];
}

// Next Appointment
$nextAppointment = "No upcoming appointments";
$sql = "SELECT appointment_date FROM appointments 
        WHERE patient_id = ? AND appointment_date > NOW() 
        ORDER BY appointment_date ASC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$stmt->bind_result($nextAppointmentDate);
if ($stmt->fetch()) {
    $nextAppointment = date("d M Y, h:i A", strtotime($nextAppointmentDate));
}
$stmt->close();

// Most Experienced Doctors (Top 3)
$experiencedDoctors = [];
$sql = "SELECT first_name, last_name, experience_years 
        FROM doctors 
        ORDER BY experience_years DESC LIMIT 3";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $experiencedDoctors[] = $row['first_name'] . ' ' . $row['last_name'] . ' (' . $row['experience_years'] . ' yrs)';
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Patient Dashboard - MediSchedule</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css" />
</head>
<body style="background: url('background.jpg') no-repeat center center fixed; background-size: cover;">
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand text-danger fw-bold" href="#">
        <img src="logo.png" alt="MediSchedule Logo" height="40" class="me-2" />
        MediSchedule
      </a>
      <div class="ms-auto">
        <a href="patient-profile.php" class="btn btn-outline-danger">View Profile</a>
      </div>
    </div>
  </nav>

  <div class="container-fluid my-5 px-4">
    <div class="p-4 bg-white rounded shadow" style="opacity: 0.96">
      <h3 class="text-center text-danger mb-4">Welcome to Your Dashboard</h3>

      <!-- Info Grid -->
      <div class="row text-center mb-5 g-4">
        <div class="col-md-4">
          <div class="p-4 border rounded bg-light h-100">
            <h6>Total Appointments</h6>
            <p class="fs-4 fw-semibold text-danger"><?= $totalAppointments ?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 border rounded bg-light h-100">
            <h6>Total Doctors Available</h6>
            <p class="fs-4 fw-semibold text-danger"><?= $totalDoctors ?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 border rounded bg-light h-100">
            <h6>Next Appointment</h6>
            <p class="fs-5 text-muted"><?= $nextAppointment ?></p>
          </div>
        </div>
      </div>

      <!-- Experienced Doctors Section -->
      <div class="row justify-content-center mb-5">
        <div class="col-md-10">
          <div class="p-4 border rounded bg-light">
            <h5 class="text-center mb-3">Most Experienced Doctors</h5>
            <ul class="text-center list-unstyled">
              <?php foreach ($experiencedDoctors as $doc): ?>
                <li><strong><?= $doc ?></strong></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="row justify-content-center text-center g-4">
        <div class="col-md-4">
          <a href="request-appointment.php" class="btn btn-danger w-100 py-2">Book Appointment</a>
          <small class="text-muted d-block mt-1">Schedule a new consultation with available doctors.</small>
        </div>
        <div class="col-md-4">
          <a href="view-bills.php" class="btn btn-outline-danger w-100 py-2">View Bills</a>
          <small class="text-muted d-block mt-1">Check your past billing history and payments.</small>
        </div>
        <div class="col-md-4">
          <a href="view-health-records.php" class="btn btn-outline-danger w-100 py-2">Check Health Records</a>
          <small class="text-muted d-block mt-1">Access your medical reports and prescriptions.</small>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
