<?php
session_start();
require 'db_connect.php'; // include your DB connection file

// Check if patient is logged in
if (!isset($_SESSION['patient']['patient_id'])) {
    header('Location: login-patient.html');
    exit();
}

$patient_id = $_SESSION['patient']['patient_id'];

// SQL to get bill info with doctor names
$sql = "
SELECT b.issued_at, d.first_name AS doctor_fname, d.last_name AS doctor_lname, b.amount, b.status
FROM bills b
JOIN appointments a ON b.appointment_id = a.appointment_id
JOIN doctors d ON a.doctor_id = d.doctor_id
WHERE a.patient_id = ?
ORDER BY b.issued_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total unpaid
$total_due = 0;
$bills = [];
while ($row = $result->fetch_assoc()) {
    $bills[] = $row;
    if (strtolower($row['status']) !== 'paid') {
        $total_due += $row['amount'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Bills - MediSchedule</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background: url('background.jpg') no-repeat center center fixed; background-size: cover;">
  <nav class="p-3">
    <a href="home-patient.php" class="btn btn-outline-danger">← Back to Home</a>
  </nav>

  <div class="container mt-2 p-4 bg-white rounded shadow" style="opacity: 0.96">
    <h3 class="text-center text-danger mb-4">Your Bills</h3>

    <table class="table table-bordered table-hover">
      <thead class="table-danger">
        <tr>
          <th>Date</th>
          <th>Doctor</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($bills) > 0): ?>
          <?php foreach ($bills as $bill): ?>
            <tr>
              <td><?= htmlspecialchars($bill['issued_at']) ?></td>
              <td>Dr. <?= htmlspecialchars($bill['doctor_fname'] . ' ' . $bill['doctor_lname']) ?></td>
              <td>₹<?= htmlspecialchars($bill['amount']) ?></td>
              <td>
                <?php if (strtolower($bill['status']) === 'paid'): ?>
                  <span class="badge bg-success">Paid</span>
                <?php else: ?>
                  <span class="badge bg-warning text-dark"><?= htmlspecialchars($bill['status']) ?></span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="4" class="text-center">No bills found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="text-end mt-3">
      <h5>Total to Pay: <span class="text-danger">₹<?= $total_due ?></span></h5>
    </div>
  </div>
</body>
</html>
