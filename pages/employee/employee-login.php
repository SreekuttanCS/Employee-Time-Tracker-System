<?php
    include_once __DIR__ . '/../php/connection.php';

    // Start the session
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Check if the form fields are set
        if (isset($_POST['emp-id']) && isset($_POST['emp-password'])) {
            // Get employee ID and password from POST request
            $emp_id = $_POST['emp-id'];
            $password = $_POST['emp-password'];

            // Check if both employee ID and password are provided
            if (empty($emp_id) || empty($password)) {
                $emp_missing = true; // Missing employee ID or password
            } else {
                // Prepare SQL statement to fetch employee by emp_id
                $stmt = $conn->prepare("SELECT emp_id, password FROM employee WHERE emp_id = ?");
                $stmt->bind_param("s", $emp_id);  // Bind the emp_id parameter to the query

                // Execute the statement
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Employee found, fetch the row
                    $row = $result->fetch_assoc();

                    // Verify the password using password_verify (assuming password is hashed)
                    if (password_verify($password, $row['password'])) {
                        // Password is correct, store emp_id in session
                        $_SESSION['emp_id'] = $row['emp_id'];

                        // Redirect to employee-attendance-system.php
                        header("Location: employee-attendance-system.php");
                        exit();
                    } else {
                        // Incorrect password
                        $emp_password_error = true;
                    }
                } else {
                    // Employee not found
                    $emp_id_error = true;
                }

                // Close the statement
                $stmt->close();
            }
        } else {
            $emp_missing = true; // Missing employee ID or password
        }

        // Close the database connection
        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Log In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="../css/admin-emp-login.css">
</head>
<body>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="login_container card rounded-4 shadow-lg m-0 p-0 overflow-hidden">
            <!-- Back button -->
            <div class="top_panel p-3 d-flex justify-content-center">
                <div class="row container-fluid justify-content-end">
                    <a href="../../index.php"><i class="bi bi-arrow-left" style="color: white;"></i></a>
                </div>
            </div>
            <!-- Employee log in -->
            <div class="row bottom_panel mx-5 my-4 p-0">
                <form class="m-0 p-0" action="" method="post">
                    <!-- Displays an error for missing fields -->
                    <?php if (isset($emp_missing)): ?>
                        <div class="alert alert-warning py-3 mb-3 d-flex justify-content-between" role="alert">
                            <div class="alert_mes">Please fill out all fields!</div>
                            <button class='btn-close' data-bs-dismiss='alert' aria-label='Close' type='button'></button>
                        </div>
                    <?php endif; ?>

                    <!-- Displays an error for incorrect employee ID -->
                    <?php if (isset($emp_id_error)): ?>
                        <div class="alert alert-danger py-3 mb-3 d-flex justify-content-between" role="alert">
                            <div class="alert_mes">Employee ID not found!</div>
                            <button class='btn-close' data-bs-dismiss='alert' aria-label='Close' type='button'></button>
                        </div>
                    <?php endif; ?>

                    <!-- Displays an error for incorrect password -->
                    <?php if (isset($emp_password_error)): ?>
                        <div class="alert alert-danger py-3 mb-3 d-flex justify-content-between" role="alert">
                            <div class="alert_mes">Incorrect password!</div>
                            <button class='btn-close' data-bs-dismiss='alert' aria-label='Close' type='button'></button>
                        </div>
                    <?php endif; ?>

                    <!-- Employee login form -->
                    <p class="text-center">Employee Log In</p>
                    <div class="mt-3">
                        <label for="emp-id">Employee ID</label>
                        <input class="form-control" type="text" placeholder="Enter employee ID" id="emp-id" name="emp-id"/>
                    </div>
                    <div class="mt-2 mb-3">
                        <label for="emp-password">Password</label>
                        <input class="form-control" type="password" placeholder="Enter password" id="emp-password" name="emp-password" />
                    </div>
                    <div class="login_button d-flex justify-content-center align-items-center">
                        <button class="my-3 w-50 text-center btn btn-primary" type="submit">Log In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
