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

// Fetch Total Patients Treated (from health_records instead of appointments)
$sql_patients = "SELECT COUNT(DISTINCT a.patient_id) AS total_patients 
                FROM health_records hr
                JOIN appointments a ON hr.appointment_id = a.appointment_id
                WHERE a.doctor_id = ?";
$stmt_patients = $conn->prepare($sql_patients);
$stmt_patients->bind_param("i", $doctor_id);
$stmt_patients->execute();
$result_patients = $stmt_patients->get_result();
$total_patients = $result_patients->fetch_assoc()['total_patients'];

// Fetch Gender Ratio (based on distinct patients in health records)
$sql_gender = "SELECT 
                SUM(CASE WHEN p.gender = 'Male' THEN 1 ELSE 0 END) AS male_patients,
                SUM(CASE WHEN p.gender = 'Female' THEN 1 ELSE 0 END) AS female_patients
                FROM (
                    SELECT DISTINCT a.patient_id
                    FROM health_records hr
                    JOIN appointments a ON hr.appointment_id = a.appointment_id
                    WHERE a.doctor_id = ?
                ) AS unique_patients
                JOIN patients p ON unique_patients.patient_id = p.patient_id";
$stmt_gender = $conn->prepare($sql_gender);
$stmt_gender->bind_param("i", $doctor_id);
$stmt_gender->execute();
$result_gender = $stmt_gender->get_result();
$gender_data = $result_gender->fetch_assoc();
$male_patients = $gender_data['male_patients'];
$female_patients = $gender_data['female_patients'];

// Fetch Top 3 Diagnoses (same)
$sql_diagnoses = "SELECT diagnosis, COUNT(*) AS count
                  FROM health_records hr
                  JOIN appointments a ON hr.appointment_id = a.appointment_id
                  WHERE a.doctor_id = ?
                  GROUP BY diagnosis
                  ORDER BY count DESC
                  LIMIT 3";
$stmt_diagnoses = $conn->prepare($sql_diagnoses);
$stmt_diagnoses->bind_param("i", $doctor_id);
$stmt_diagnoses->execute();
$result_diagnoses = $stmt_diagnoses->get_result();
$top_diagnoses = [];
while ($row = $result_diagnoses->fetch_assoc()) {
    $top_diagnoses[] = $row['diagnosis'];
}

// Fetch Upcoming Appointments (same as before)
$sql_upcoming = "SELECT a.appointment_id, a.appointment_date, p.first_name, p.last_name
                FROM appointments a
                JOIN patients p ON a.patient_id = p.patient_id
                WHERE a.doctor_id = ? 
                AND a.appointment_date > NOW()
                AND a.status != 'completed'
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
    <title>Doctor Dashboard - MediSchedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body style="background: url('background.jpg') no-repeat center center fixed; background-size: cover;">
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

    <!-- Dashboard Content -->
    <div class="container-fluid my-5 px-4">
        <div class="p-4 bg-white rounded shadow" style="opacity: 0.96">
            <h3 class="text-center text-danger mb-4">Welcome to Your Dashboard</h3>

            <!-- Doctor Info -->
            <div class="row mb-5">
                <div class="col-md-6">
                    <h6>Doctor Information</h6>
                    <p>Name: <?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></p>
                    <p>Email: <?= htmlspecialchars($doctor['email']) ?></p>
                    <p>Specialization: <?= htmlspecialchars($doctor['specialization']) ?></p>
                    <p>Phone: <?= htmlspecialchars($doctor['phone_number']) ?></p>
                    <p>Address: <?= htmlspecialchars($doctor['address']) ?></p>
                    <p>Experience: <?= htmlspecialchars($doctor['experience_years']) ?> years</p>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="row text-center mb-5 g-4">
                <div class="col-md-4">
                    <div class="p-4 border rounded bg-light h-100">
                        <h6>Total Patients Treated</h6>
                        <p class="fs-4 fw-semibold text-danger"><?= $total_patients ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border rounded bg-light h-100">
                        <h6>Gender Ratio</h6>
                        <p class="fs-5 text-muted">
                            Male: <?= $male_patients ?><br />Female: <?= $female_patients ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border rounded bg-light h-100">
                        <h6>Top 3 Diagnoses Treated</h6>
                        <ol class="mb-0 text-start">
                            <li><?= $top_diagnoses[0] ?? 'N/A' ?></li>
                            <li><?= $top_diagnoses[1] ?? 'N/A' ?></li>
                            <li><?= $top_diagnoses[2] ?? 'N/A' ?></li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-10">
                    <div class="p-4 border rounded bg-light">
                        <h5 class="text-center mb-3">Upcoming Appointments</h5>
                        <ul class="list-group">
                            <?php if (empty($upcoming_appointments)): ?>
                                <li class="list-group-item text-center">No upcoming appointments.</li>
                            <?php else: ?>
                                <?php foreach ($upcoming_appointments as $appointment): ?>
                                    <li class="list-group-item">
                                        <?= $appointment['appointment_date'] ?> - Patient: <?= $appointment['first_name'] . ' ' . $appointment['last_name'] ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row justify-content-center text-center g-4">
                <div class="col-md-4">
                    <a href="cancel-appointment-requests.php" class="btn btn-danger w-100 py-2">Cancel Appointment Requests</a>
                    <small class="text-muted d-block mt-1">Cancel any appointment requests from patients.</small>
                </div>
                <div class="col-md-4">
                    <a href="add-health-record.php" class="btn btn-outline-danger w-100 py-2">Add Health Record</a>
                    <small class="text-muted d-block mt-1">Add a diagnosis or treatment note for a patient.</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>