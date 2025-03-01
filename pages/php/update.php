<?php 
    include "connection.php";

    // SQL Update Query
    $sql = "UPDATE atlog 
            SET 
                -- Calculate work hours based on check-in and check-out times
                work_hour = ADDTIME(IFNULL(TIMEDIFF(check_out, check_in), '00:00:00'), '00:00:00'),  -- Example, assuming you want to calculate work hour based on check_in and check_out

                -- Calculate late hours for morning check-in (if the check-in time is later than 8 AM)
                late_hours = IF(TIMEDIFF(check_in, '08:00:00') > '00:00:00', TIMEDIFF(check_in, '08:00:00'), '00:00:00'),

                -- Example of status: Could be 'Late', 'On Time', etc.
                status = CASE
                    WHEN TIMEDIFF(check_in, '08:00:00') > '00:00:00' THEN 'Late'
                    WHEN TIMEDIFF(check_out, '17:00:00') < '00:00:00' THEN 'Early'
                    ELSE 'On Time'
                END

            WHERE atlog_id = ?";  // Assuming atlog_id is the primary key for the table

    // Assuming you pass the `atlog_id` and relevant fields in your POST request
    if (isset($_POST['atlog_id']) && isset($_POST['check_in']) && isset($_POST['check_out'])) {
        $atlog_id = $_POST['atlog_id'];
        $check_in = $_POST['check_in'];
        $check_out = $_POST['check_out'];

        // Prepare statement and bind parameters
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters for prepared statement
            $stmt->bind_param("iss", $atlog_id, $check_in, $check_out);
            
            // Execute the statement
            $stmt->execute();
            
            // Check for success
            if ($stmt->affected_rows > 0) {
                echo "Update successful!";
            } else {
                echo "No records were updated.";
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Close the database connection
    $conn->close();
?>
