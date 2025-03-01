<?php
    include_once __DIR__ . '/../php/connection.php';

    // Retrieve form data
    $emp_id = $_POST['emp-id']; 
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

    // Check if required fields are filled out
    if (empty($first_name) || empty($middle_name) || empty($last_name) || empty($address) || empty($zip) || empty($contact_number) || empty($email_address) || empty($contract) || empty($shift)) {
        echo "Error: All fields are required.";
        exit;
    } else {
        // Prepare the password (hash it if the password is not empty)
        if (!empty($password)) {
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Update employee information including the password
            $sql = "UPDATE employee SET first_name = ?, middle_name = ?, last_name = ?, address = ?, zip = ?, contact_number = ?, email_address = ?, contract = ?, shift = ?, password = ? WHERE emp_id = ?";
        } else {
            // If password is not provided, don't update the password field
            $sql = "UPDATE employee SET first_name = ?, middle_name = ?, last_name = ?, address = ?, zip = ?, contact_number = ?, email_address = ?, contract = ?, shift = ? WHERE emp_id = ?";
        }

        // Prepare SQL statement
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind parameters
            if (!empty($password)) {
                mysqli_stmt_bind_param($stmt, 'ssssssssssi', $first_name, $middle_name, $last_name, $address, $zip, $contact_number, $email_address, $contract, $shift, $hashed_password, $emp_id);
            } else {
                mysqli_stmt_bind_param($stmt, 'sssssssssi', $first_name, $middle_name, $last_name, $address, $zip, $contact_number, $email_address, $contract, $shift, $emp_id);
            }

            // Execute the query
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to employee maintenance page after successful update
                header('Location: employee-maintenance.php');
                exit;
            } else {
                echo "Error: " . mysqli_stmt_error($stmt);
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

    // Close connection
    mysqli_close($conn);
?>
