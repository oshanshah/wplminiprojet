<?php
session_start();

// Assuming session variables are set after login
// Example: $_SESSION['patient'] = $patient_data_array;

if (!isset($_SESSION['patient'])) {
  header('Location: login-patient.php');
  exit();
}

$patient = $_SESSION['patient'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Patient Profile - MediSchedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body style="background: url('background.jpg') no-repeat center center fixed; background-size: cover;">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
      <div class="container-fluid">
        <a class="navbar-brand text-danger fw-bold" href="patient-home.html">
          <img src="logo.png" alt="MediSchedule Logo" height="40" class="me-2" />
          MediSchedule
        </a>
        <div class="ms-auto">
          <a href="home-patient.php" class="btn btn-outline-danger me-2">Dashboard</a>
          <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
      </div>
    </nav>

    <!-- Profile Content -->
    <div class="container my-5 p-4 bg-white rounded shadow" style="opacity: 0.95; max-width: 600px">
      <h3 class="text-center text-danger mb-4">Your Profile</h3>
      <div class="mb-3">
        <p><strong>Name:</strong> <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($patient['email']) ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($patient['dob']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($patient['gender']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($patient['phone_number']) ?></p>
        <p><strong>Address:</strong><br /><?= nl2br(htmlspecialchars($patient['address'])) ?></p>
      </div>
    </div>
  </body>
</html>
