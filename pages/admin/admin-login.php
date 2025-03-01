<?php
    include_once __DIR__ . '/../php/connection.php';

    session_start();

    if (isset($_POST['form_id'])) {
        // Get username and password from POST request
        $username = $_POST['sn-username'];
        $password = $_POST['sn-password'];

        // Check if both username and password are provided
        if (empty($username) || empty($password)) {
            $sn_missing = true; // Missing username or password
        } else {
            // Prepare SQL statement to fetch the admin by username
            $stmt = $conn->prepare("SELECT admin_id, username, password FROM admin WHERE username = ?");
            $stmt->bind_param("s", $username);  // Only username should be passed here

            // Execute the statement
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Admin found, verify password
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    // Password is correct, store admin ID in session
                    $_SESSION['admin_id'] = $row['admin_id'];
                    // Redirect to login-report.php
                    header("Location: login-report.php");
                    exit();
                } else {
                    // Incorrect password
                    $sn_password_error = true;
                }
            } else {
                // Admin not found
                $sn_username_error = true;
            }

            // Close the statement
            $stmt->close();
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
    <title>Admin Log in</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin-emp-login.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
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
            <!-- Admin log in -->
            <div class="row bottom_panel mx-5 my-4 p-0">
                <form class="m-0 p-0" action="" method="post">
                    <!-- Displays an error for wrong username or password -->
                    <?php if (isset($sn_username_error)): ?>
                        <div class="alert alert-danger py-3 mb-3 d-flex justify-content-between" role="alert">
                            <div class="alert_mes">Username not found!</div>
                            <button class='btn-close' data-bs-dismiss='alert' aria-label='Close' type='button'></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($sn_password_error)): ?>
                        <div class="alert alert-danger py-3 mb-3 d-flex justify-content-between" role="alert">
                            <div class="alert_mes">Incorrect password!</div>
                            <button class='btn-close' data-bs-dismiss='alert' aria-label='Close' type='button'></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($sn_missing)): ?>
                        <div class="alert alert-warning py-3 mb-3 d-flex justify-content-between" role="alert">
                            <div class="alert_mes">Please fill out all fields!</div>
                            <button class='btn-close' data-bs-dismiss='alert' aria-label='Close' type='button'></button>
                        </div>
                    <?php endif; ?>

                    <!-- ADMIN LOGIN FORM -->
                    <p class="text-center">Admin Log In</p>
                    <div class="mt-3">
                        <label for="sn-username">Username</label>
                        <input class="form-control" type="text" placeholder="Enter admin username" id="sn-username" name="sn-username"/>
                    </div>
                    <div class="mt-2 mb-3">
                        <label for="sn-password">Password</label>
                        <input class="form-control" type="password" placeholder="Enter password" id="sn-password" name="sn-password" />
                    </div>
                    <input type="hidden" name="form_id" value="1">
                    <div class="login_button d-flex justify-content-center align-items-center">
                        <button class="my-3 w-50 text-center btn btn-primary" type="submit">Log In</button>
                    </div>
                    <div class="mt-3 create_account text-center">
                        <p>Need an account? <span class="bold_title"><a class="nav_link" href="admin-sign-up.php">Create an account</a></span></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
