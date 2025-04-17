<?php
session_start();

// Check if patient is logged in
if (!isset($_SESSION['patient'])) {
    header('Location: login.php');
    exit();
}

// Get patient info from session
$patient = $_SESSION['patient'];
$patientId = $patient['patient_id'];
$patientName = $patient['first_name'] . ' ' . $patient['last_name'];

// Connect to database
$conn = new mysqli("localhost", "root", "", "medischedule");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch health records for the logged-in patient
$sql = "
    SELECT 
        hr.recorded_at AS date,
        CONCAT(d.first_name, ' ', d.last_name) AS doctor,
        d.specialization,
        hr.diagnosis
    FROM health_records hr
    JOIN appointments a ON hr.appointment_id = a.appointment_id
    JOIN doctors d ON a.doctor_id = d.doctor_id
    WHERE a.patient_id = ?
    ORDER BY hr.recorded_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Health Records - MediSchedule</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background: url('background.jpg') no-repeat center center fixed; background-size: cover;">
  <nav class="p-3">
    <a href="home-patient.php" class="btn btn-outline-danger">← Back to Home</a>
  </nav>

  <div class="container mt-2 p-4 bg-white rounded shadow" style="opacity: 0.96">
    <h3 class="text-center text-danger mb-4">Your Health Records</h3>
    <p class="text-center mb-4">Showing records for: <strong><?= htmlspecialchars($patientName) ?></strong></p>

    <table class="table table-bordered table-hover">
      <thead class="table-danger">
        <tr>
          <th>Date</th>
          <th>Doctor</th>
          <th>Specialization</th>
          <th>Diagnosis</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= date("Y-m-d", strtotime($row['date'])) ?></td>
            <td><?= htmlspecialchars($row['doctor']) ?></td>
            <td><?= htmlspecialchars($row['specialization']) ?></td>
            <td><?= htmlspecialchars($row['diagnosis']) ?></td>
          </tr>
        <?php endwhile; ?>
        <?php if ($result->num_rows === 0): ?>
          <tr>
            <td colspan="4" class="text-center">No records found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
