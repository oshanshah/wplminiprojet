<?php
session_start();
require 'db_connect.php'; // Ensure this connects to your DB

// Get selected values from POST
$selected_specialty = $_POST['specialization'] ?? '';
$selected_doctor_id = $_POST['doctor_id'] ?? '';

// Fetch distinct specialties
$specialty_result = $conn->query("SELECT DISTINCT specialization FROM doctors ORDER BY specialization ASC");
$specialties = [];
if ($specialty_result && $specialty_result->num_rows > 0) {
    while ($row = $specialty_result->fetch_assoc()) {
        $specialties[] = $row['specialization'];
    }
}

// Fetch doctors based on selected specialty
$doctors = [];
if (!empty($selected_specialty)) {
    $stmt = $conn->prepare("SELECT doctor_id, first_name, last_name FROM doctors WHERE specialization = ?");
    $stmt->bind_param("s", $selected_specialty);
    $stmt->execute();
    $doctors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Request Appointment - MediSchedule</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background: url('background.jpg') no-repeat center center fixed; background-size: cover;">
  <nav class="p-3">
    <a href="home-patient.php" class="btn btn-outline-danger">← Back to Home</a>
  </nav>

  <div class="container mt-2 p-4 bg-white rounded shadow" style="max-width: 700px; opacity: 0.95">
    <h3 class="text-center text-danger mb-4">Request an Appointment</h3>

    <form method="POST" id="mainForm" action="request-appointment.php">
      <!-- Specialization Dropdown -->
      <div class="mb-3">
        <label for="specialization" class="form-label">Select Specialization</label>
        <select class="form-select" name="specialization" id="specialization" required onchange="document.getElementById('mainForm').submit();">
          <option value="">-- Select Specialty --</option>
          <?php foreach ($specialties as $specialty): ?>
            <option value="<?= htmlspecialchars($specialty) ?>" <?= $specialty === $selected_specialty ? 'selected' : '' ?>>
              <?= htmlspecialchars($specialty) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Doctor Dropdown -->
      <?php if (!empty($selected_specialty)): ?>
        <div class="mb-3">
          <label for="doctor_id" class="form-label">Select Doctor</label>
          <select class="form-select" name="doctor_id" id="doctor_id" required onchange="document.getElementById('mainForm').submit();">
            <option value="">-- Select Doctor --</option>
            <?php foreach ($doctors as $doc): ?>
              <option value="<?= $doc['doctor_id'] ?>" <?= $selected_doctor_id == $doc['doctor_id'] ? 'selected' : '' ?>>
                Dr. <?= htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>

      <!-- Date, Time & Reason Fields -->
      <?php if (!empty($selected_specialty) && !empty($selected_doctor_id)): ?>
        <div class="mb-3">
          <label for="date" class="form-label">Preferred Date</label>
          <input type="date" class="form-control" name="date" required />
        </div>
        <div class="mb-3">
          <label for="time" class="form-label">Preferred Time</label>
          <input type="time" class="form-control" name="time" required />
        </div>
        <div class="mb-3">
          <label for="reason" class="form-label">Reason for Appointment</label>
          <textarea name="reason" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" formaction="submit_appointment.php" class="btn btn-danger w-100">
          Request Appointment
        </button>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
