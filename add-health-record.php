<?php
session_start();

if (!isset($_SESSION['doctor'])) {
    header("Location: login-doctor.html");
    exit;
}

$doctor = $_SESSION['doctor'];
$doctor_id = $doctor['doctor_id'];

$host = "localhost";
$dbname = "medischedule";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Upcoming Appointments 
$sql = "SELECT a.appointment_id, a.appointment_date, p.first_name, p.last_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        WHERE a.doctor_id = ? 
          AND a.appointment_date > NOW()
          AND a.status != 'completed'
        ORDER BY a.appointment_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
echo($doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Health Records - MediSchedule</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background: url('background.jpg') no-repeat center center fixed; background-size: cover;">
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand text-danger fw-bold" href="#">
        <img src="logo.png" alt="MediSchedule Logo" height="40" class="me-2" />
        MediSchedule
      </a>
      <div class="ms-auto d-flex align-items-center">
        <span class="btn btn-outline-danger"><?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></span>
        <a href="logout.php" class="btn btn-outline-danger ms-3">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container-fluid my-5 px-4">
    <div class="p-4 bg-white rounded shadow" style="opacity: 0.96">
      <h3 class="text-center text-danger mb-4">Upcoming Appointments - Add Health Records</h3>

      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="p-4 border rounded bg-light">
            <h5 class="text-center mb-3">Upcoming Appointments</h5>
            <ul class="list-group">
              <?php if (empty($appointments)): ?>
                <li class="list-group-item text-center">No upcoming appointments.</li>
              <?php else: ?>
                <?php foreach ($appointments as $appt): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <?= htmlspecialchars($appt['appointment_date']) ?> - Patient: <?= htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']) ?>
                    </div>
                    <a href="submit-health-record.php?appointment_id=<?= $appt['appointment_id'] ?>" class="btn btn-primary btn-sm">Add Health Record</a>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>

      <div class="row justify-content-center text-center g-4 mt-4">
        <div class="col-md-4">
          <a href="doctor-home.php" class="btn btn-outline-danger w-100 py-2">Go Back to Doctor Home</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
