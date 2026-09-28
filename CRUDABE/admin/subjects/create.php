<?php
session_start();
include "../../config/database.php";

// Only admin users can access this page
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../../../index.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save"])) {
    // Sanitize and validate inputs
    $subject_code = trim($_POST["subject_code"] ?? '');
    $subject_name = trim($_POST["subject_name"] ?? '');
    $units        = filter_input(INPUT_POST, 'units', FILTER_VALIDATE_INT);

    if (!empty($subject_code) && !empty($subject_name) && $units !== false && $units > 0) {
        // Use prepared statements to prevent SQL Injection
        $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, units) VALUES (?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ssi", $subject_code, $subject_name, $units);
            
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: index.php?message=" . urlencode("Subject Added Successfully"));
                exit;
            } else {
                $message = "Could not save the record: " . htmlspecialchars($stmt->error);
                $stmt->close();
            }
        } else {
            $message = "Database preparation error: " . htmlspecialchars($conn->error);
        }
    } else {
        $message = "Please fill in all required fields with valid values.";
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subject Form</title>

    <!-- Bootstrap CSS -->
    <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <!-- Main Container -->
    <div class="container py-5" style="max-width: 700px;">

        <!-- Subject Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <h2 class="mb-4">Subject Form</h2>

                <!-- Display Error Message -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">

                    <!-- Subject Code -->
                    <div class="mb-3">
                        <label for="subject_code" class="form-label">Subject Code</label>
                        <input type="text" class="form-control" id="subject_code" name="subject_code" required>
                    </div>

                    <!-- Subject Name -->
                    <div class="mb-3">
                        <label for="subject_name" class="form-label">Subject Name</label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" required>
                    </div>

                    <!-- Units -->
                    <div class="mb-3">
                        <label for="units" class="form-label">Units</label>
                        <input type="number" class="form-control" id="units" name="units" min="1" required>
                    </div>

                    <!-- Form Actions -->
                    <button type="submit" class="btn btn-primary" name="save">
                        Save Subject
                    </button>

                    <a href="../dashboard.php" class="btn btn-secondary">
                        Cancel
                    </a>

                </form>

            </div>
        </div>

    </div>

</body>
</html>