<?php
    include_once __DIR__ . '/../php/connection.php';

    // Retrieve form data from POST
    $first_name = $_POST['input-first-name'];
    $middle_name = $_POST['input-middle-name'];
    $last_name = $_POST['input-last-name'];
    $password = $_POST['input-password'];
    $address = $_POST['input-address'];
    $zip = $_POST['input-zip'];
    $contact_number = $_POST['input-contact-number'];
    $email_address = $_POST['input-email-address'];
    $contract = $_POST['input-employee-contract'];
    $shift = $_POST['input-shift'];
    $branch = $_POST['input-branch'];
    $department = $_POST['input-department'];

    // Validate: Check if any required fields are empty
    if (empty($first_name) || empty($last_name) || empty($address) || empty($zip) || empty($contact_number) || empty($email_address) || empty($contract) || empty($shift) || empty($branch) || empty($department)) {
        echo "<script>alert('Please fill out all the required fields!'); window.history.back();</script>";
    } else {
        // Hash the password for secure storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // SQL query to insert the employee data into the database
        $sql = "INSERT INTO employee (first_name, middle_name, last_name, address, zip, contact_number, email_address, contract, shift, password, branch, department) 
                VALUES ('$first_name', '$middle_name', '$last_name', '$address', '$zip', '$contact_number', '$email_address', '$contract', '$shift', '$hashed_password', '$branch', '$department')";

        // Execute the query
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            echo "<script>alert('Employee added successfully!'); window.location.href='employee-maintenance.php';</script>";
        } else {
            echo "<script>alert('ERROR: " . mysqli_error($conn) . "'); window.history.back();</script>";
        }
    }

    // Close the database connection
    mysqli_close($conn);
?>
