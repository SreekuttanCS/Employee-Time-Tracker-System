<?php
    include_once __DIR__ . '/../php/connection.php';

    if (isset($_POST['form_id'])) {
        // Get user data from POST
        $username = $_POST['cr-username'];
        $password = $_POST['cr-password'];
        $full_name = $_POST['cr-name'];

        // Check if any fields are empty
        if (empty($username) || empty($password) || empty($full_name)) {
            $cr_error = true; // Missing fields
        } else {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT username FROM admin WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Username already exists
                $cr_exist = true;
            } else {
                // Hash the password and insert new user
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO admin (username, password, full_name) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed_password, $full_name);
                $stmt->execute();
                $cr_success = true;
            }
            $stmt->close();
        }

        $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up</title>
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
            <!-- Admin Sign Up -->
            <div class="row bottom_panel mx-5 my-4 p-0">
                <form class="m-0 p-0" action="" method="post">
                    <!-- Display error messages -->
                    <?php if (isset($cr_error)): ?>
                        <div class="alert alert-warning py-3 mb-3 d-flex justify-content-between" role="alert">
                            <div class="alert_mes">Please fill out all fields!</div>
                            <button class='btn-close' data-bs-dismiss='alert' aria-label='Close' type='button'></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($cr_exist)): ?>
                        <div class="alert alert-danger py-3 mb-3 d-flex justify-content-between" role="alert">
                            <div class="alert_mes">Admin with this username already exists!</div>
                            <button class='btn-close' data-bs-dismiss='alert' aria-label='Close' type='button'></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($cr_success)): ?>
                        <div class="alert alert-success py-3 mb-3 d-flex justify-content-between" role="alert">
                            <div class="alert_mes">Account created successfully!</div>
                            <button class='btn-close' data-bs-dismiss='alert' aria-label='Close' type='button'></button>
                        </div>
                    <?php endif; ?>

                    <!-- Admin Sign Up Form -->
                    <p class="text-center">Admin Sign Up</p>
                    <div class="mt-3">
                        <label for="cr-username">Username</label>
                        <input class="form-control" type="text" placeholder="Enter admin username" id="cr-username" name="cr-username"/>
                    </div>
                    <div class="mt-3">
                        <label for="cr-password">Password</label>
                        <input class="form-control" type="password" placeholder="Enter password" id="cr-password" name="cr-password" />
                    </div>
                    <div class="mt-3">
                        <label for="cr-name">Full Name</label>
                        <input class="form-control" type="text" placeholder="Enter full name" id="cr-name" name="cr-name" />
                    </div>
                    <input type="hidden" name="form_id" value="1">
                    <div class="login_button d-flex justify-content-center align-items-center">
                        <button class="my-3 w-50 text-center btn btn-primary" type="submit">Create Account</button>
                    </div>
                    <div class="mt-3 create_account text-center">
                        <p>Already have an account? <span class="bold_title"><a class="nav_link" href="admin-login.php">Login</a></span></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
