<?php
session_start();

// Uncomment this in production to restrict access to logged-in admins
// if (!isset($_SESSION['admin'])) {
//     header("Location: login-admin.html");
//     exit;
// }

$admin = $_SESSION['admin'] ?? ['first_name' => 'Admin']; // Fallback for demo

// DB Config
$host = "localhost";
$dbname = "medischedule";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$alert_type = "";

// Add Bill
if (isset($_POST['add_bill'])) {
    $appointment_id = $_POST['appointment_id'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    $created_at = date('Y-m-d H:i:s'); // current timestamp

    $sql = "CALL add_bill(?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $appointment_id, $amount, $status, $created_at);

    if ($stmt->execute()) {
        $message = "Bill added successfully.";
        $alert_type = "success";
    } else {
        $message = "Failed to add bill.";
        $alert_type = "danger";
    }
}

// Mark Bill as Paid
if (isset($_POST['mark_paid'])) {
    $bill_id = $_POST['bill_id'];

    $sql = "CALL mark_bill_as_paid(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();

    $message = "Bill marked as paid.";
    $alert_type = "success";
}

// Add Doctor
if (isset($_POST['add_doctor'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $experience_years = $_POST['experience_years'];
    $password = $_POST['password'];

    $sql = "CALL add_doctor(?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $specialization, $phone_number, $address, $experience_years, $password);
    $stmt->execute();

    $message = "Doctor added successfully.";
    $alert_type = "success";
}

// Delete Doctor
if (isset($_POST['delete_doctor'])) {
    $doctor_id = $_POST['doctor_id'];

    $sql = "CALL delete_doctor(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();

    $message = "Doctor deleted successfully.";
    $alert_type = "warning";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - MediSchedule</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body style="background: url('background.jpg') no-repeat center center fixed; background-size: cover;">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand text-danger fw-bold" href="#">
        <img src="logo.png" alt="MediSchedule Logo" height="40" class="me-2" />
        MediSchedule
      </a>
      <div class="ms-auto">
        <a href="logout.php" class="btn btn-outline-danger">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Alerts -->
  <?php if ($message): ?>
    <div class="container mt-4">
      <div class="alert alert-<?= $alert_type ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Content -->
  <div class="container my-5">
    <div class="bg-white p-5 rounded shadow" style="opacity: 0.96;">
      <h2 class="text-center text-danger mb-5">Admin Control Panel</h2>

      <div class="row g-5">
        <!-- Add Bill -->
        <div class="col-md-6">
          <h5 class="text-danger">Add Bill</h5>
          <form method="POST">
            <input type="hidden" name="add_bill" value="1" />
            <div class="mb-2">
              <input type="number" class="form-control" name="appointment_id" placeholder="Appointment ID" required />
            </div>
            <div class="mb-2">
              <input type="number" step="0.01" class="form-control" name="amount" placeholder="Amount" required />
            </div>
            <div class="mb-2">
              <select class="form-control" name="status" required>
                <option value="">Select Status</option>
                <option value="unpaid">Unpaid</option>
                <option value="paid">Paid</option>
              </select>
            </div>
            <button class="btn btn-danger w-100">Add Bill</button>
          </form>
        </div>

        <!-- Mark Bill as Paid -->
        <div class="col-md-6">
          <h5 class="text-danger">Mark Bill as Paid</h5>
          <form method="POST">
            <input type="hidden" name="mark_paid" value="1" />
            <div class="mb-2">
              <input type="number" class="form-control" name="bill_id" placeholder="Bill ID" required />
            </div>
            <button class="btn btn-success w-100">Mark as Paid</button>
          </form>
        </div>

        <!-- Add Doctor -->
        <div class="col-md-6">
          <h5 class="text-danger">Add Doctor</h5>
          <form method="POST">
            <input type="hidden" name="add_doctor" value="1" />
            <div class="row g-2">
              <div class="col">
                <input type="text" class="form-control" name="first_name" placeholder="First Name" required />
              </div>
              <div class="col">
                <input type="text" class="form-control" name="last_name" placeholder="Last Name" required />
              </div>
            </div>
            <div class="mt-2">
              <input type="email" class="form-control" name="email" placeholder="Email" required />
            </div>
            <div class="mt-2">
              <input type="text" class="form-control" name="specialization" placeholder="Specialization" required />
            </div>
            <div class="mt-2">
              <input type="text" class="form-control" name="phone_number" placeholder="Phone Number" required />
            </div>
            <div class="mt-2">
              <input type="text" class="form-control" name="address" placeholder="Address" required />
            </div>
            <div class="mt-2">
              <input type="number" class="form-control" name="experience_years" placeholder="Experience (Years)" required />
            </div>
            <div class="mt-2">
              <input type="password" class="form-control" name="password" placeholder="Password" required />
            </div>
            <button class="btn btn-primary mt-3 w-100">Add Doctor</button>
          </form>
        </div>

        <!-- Delete Doctor -->
        <div class="col-md-6">
          <h5 class="text-danger">Delete Doctor</h5>
          <form method="POST">
            <input type="hidden" name="delete_doctor" value="1" />
            <div class="mb-2">
              <input type="number" class="form-control" name="doctor_id" placeholder="Doctor ID" required />
            </div>
            <button class="btn btn-danger w-100">Delete Doctor</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
