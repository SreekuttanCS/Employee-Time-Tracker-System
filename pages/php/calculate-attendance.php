<?php
// Include database connection
include_once __DIR__ . '/../php/connection.php';
session_start();

// Function to calculate work hours and overtime
function calculateWorkHours($check_in, $check_out, $total_work_hours) {
    // Initialize the results as empty
    $work_hours = "-";
    $overtime_hours = "-";

    // Only proceed if both check-in and check-out are available
    if ($check_in && $check_out) {
        // Calculate the work duration if there's a check-in and check-out time
        $check_in_time = new DateTime($check_in);
        $check_out_time = new DateTime($check_out);
        $work_duration = $check_in_time->diff($check_out_time);
        $work_hours = $work_duration->h + ($work_duration->i / 60); // Work hours in decimal format

        // Convert 'total_work_hours' from time format to decimal
        $total_work_hours_decimal = convertTimeToDecimal($total_work_hours);

        // If work hours exceed total work hours, calculate overtime
        if ($work_hours > $total_work_hours_decimal) {
            $overtime_hours = $work_hours - $total_work_hours_decimal;
        } else {
            $overtime_hours = "-"; // No overtime
        }

        // Format work hours and overtime hours for display
        $formatted_work_hours = sprintf('%02d:%02d:%02d', floor($work_hours), floor(($work_hours - floor($work_hours)) * 60), 0);
        $formatted_overtime_hours = $overtime_hours === "-" ? "-" : sprintf('%02d:%02d:%02d', floor($overtime_hours), floor(($overtime_hours - floor($overtime_hours)) * 60), 0);
    }

    return [
        'work_hour' => $formatted_work_hours,
        'overtime_hour' => $formatted_overtime_hours,
    ];
}

// Convert time to decimal (HH:MM:SS to decimal hours)
function convertTimeToDecimal($time) {
    // If $time is not empty or null
    if (!empty($time)) {
        $time_parts = explode(":", $time);
        if (count($time_parts) === 3) {
            $hours = (int)$time_parts[0];
            $minutes = (int)$time_parts[1];
            return $hours + ($minutes / 60);
        }
    }
    return 0; // Return 0 if invalid or empty time format
}

if (isset($_POST['emp_id'])) {
    $emp_id = $_POST['emp_id'];

    // Query to get employee check-in time and total work hours
    $query = "SELECT check_in, atlog_DATE, total_work_hours FROM atlog INNER JOIN employee ON atlog.emp_id = employee.emp_id WHERE atlog.emp_id = ? ORDER BY atlog_DATE DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $attendance = $result->fetch_assoc();
        $check_in = $attendance['check_in'];
        $atlog_date = $attendance['atlog_DATE'];
        $total_work_hours = $attendance['total_work_hours']; // Time format (HH:MM:SS)

        if (isset($_POST['check_out']) && $_POST['check_out'] !== "") {
            $check_out = $_POST['check_out'];
        } else {
            $check_out = NULL;
        }

        // Call the function to calculate work hours and overtime
        $attendance_data = calculateWorkHours($check_in, $check_out, $total_work_hours);
        $work_hour = $attendance_data['work_hour'];
        $overtime_hour = $attendance_data['overtime_hour'];

        // Update the attendance record with the calculated data
        $update_query = "UPDATE atlog SET work_hour = ?, overtime_hour = ? WHERE emp_id = ? AND atlog_DATE = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("ssis", $work_hour, $overtime_hour, $emp_id, $atlog_date);

        if ($stmt_update->execute()) {
            // Return calculated data as JSON
            echo json_encode([
                'status' => 'success',
                'work_hour' => $work_hour,
                'overtime_hour' => $overtime_hour,
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update attendance record']);
        }

        $stmt_update->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No check-in found for this employee!']);
    }
    $stmt->close();
    mysqli_close($conn);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing employee ID!']);
}
?>
