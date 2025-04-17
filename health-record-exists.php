<?php
session_start();

// Check if doctor session is set
if (!isset($_SESSION['doctor'])) {
    header("Location: login-doctor.html");
    exit;
}

$doctor = $_SESSION['doctor'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Health Record Exists - MediSchedule</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <!-- Navbar -->
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

  <!-- Content -->
  <div class="container-fluid my-5 px-4">
    <div class="p-4 bg-white rounded shadow" style="opacity: 0.96">
      <h3 class="text-center text-danger mb-4">Health Record Already Exists</h3>

      <p class="text-center">A health record has already been added for this appointment. You cannot add another record for the same appointment.</p>

      <!-- Back Button -->
      <div class="text-center mt-4">
        <a href="doctor-home.php" class="btn btn-danger">Back to Dashboard</a>
      </div>
    </div>
  </div>
</body>
</html>
