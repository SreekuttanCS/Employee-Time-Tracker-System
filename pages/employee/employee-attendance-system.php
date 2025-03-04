<?php
session_start();
include_once __DIR__ . '/../php/connection.php'; // Ensure the path is correct

// Set the correct time zone
date_default_timezone_set('Asia/Kolkata'); // Set the time zone to IST

$empId = $_SESSION['emp_id']; // Get employee ID from session

// Initialize variables for employee information
$employeeShift = '';
$employeeContract = '';
$employeeFullName = '';
$atlogId = 0; // Default to 0 if no previous entry
$checkIn = null;
$checkOut = null;

// Check if action is triggered (check_in or check_out)
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $atlogId = $_POST['atlog_id']; // Get the atlog_id from the hidden input field

    // Process check-in or check-out action
    if ($action === 'check_in') {
        $checkInTime = new DateTime(); // Current date-time as DateTime object
        $checkInTimeFormatted = $checkInTime->format('Y-m-d H:i:s'); // Format it as string
        // Update the check-in time and set status to 'Online'
        $updateQuery = "UPDATE atlog SET check_in = '$checkInTimeFormatted', status = 'Online' WHERE atlog_id = $atlogId";
        
        if ($conn->query($updateQuery) === TRUE) {
            echo "<script>alert('You have successfully checked in at $checkInTimeFormatted.');</script>";
        } else {
            echo "<script>alert('Error checking in: " . $conn->error . "');</script>";
        }
    } elseif ($action === 'check_out') {
        $checkOutTime = new DateTime(); // Current date-time as DateTime object
        $checkOutTimeFormatted = $checkOutTime->format('Y-m-d H:i:s'); // Format it as string
        $updateQuery = "UPDATE atlog SET check_out = '$checkOutTimeFormatted', status = 'Offline' WHERE atlog_id = $atlogId";

        if ($conn->query($updateQuery) === TRUE) {
            // Calculate work hours and overtime
            $calculateQuery = "SELECT check_in, check_out, total_work_hours FROM atlog 
                               INNER JOIN employee ON atlog.emp_id = employee.emp_id 
                               WHERE atlog.atlog_id = $atlogId";
            $calculateResult = $conn->query($calculateQuery);

            if ($calculateResult->num_rows > 0) {
                $row = $calculateResult->fetch_assoc();
                $checkIn = $row['check_in'];
                $checkOut = $row['check_out'];
                $totalWorkHours = $row['total_work_hours'];

                // Calculate work hours and overtime
                $checkInTime = new DateTime($checkIn);
                $checkOutTime = new DateTime($checkOut);
                $workDuration = $checkInTime->diff($checkOutTime);
                $workHours = $workDuration->h + ($workDuration->i / 60); // Work hours in decimal format

                // Convert total work hours to decimal
                $totalWorkHoursDecimal = convertTimeToDecimal($totalWorkHours);

                // Calculate overtime
                $overtimeHours = ($workHours > $totalWorkHoursDecimal) ? $workHours - $totalWorkHoursDecimal : 0;

                // Format work hours and overtime
                $formattedWorkHours = sprintf('%02d:%02d:%02d', floor($workHours), floor(($workHours - floor($workHours)) * 60), 0);
                $formattedOvertimeHours = ($overtimeHours > 0) ? sprintf('%02d:%02d:%02d', floor($overtimeHours), floor(($overtimeHours - floor($overtimeHours)) * 60), 0) : '-';

                // Update the attendance record with work hours and overtime
                $updateWorkHoursQuery = "UPDATE atlog SET work_hour = '$formattedWorkHours', overtime_hour = '$formattedOvertimeHours' WHERE atlog_id = $atlogId";
                if ($conn->query($updateWorkHoursQuery) === TRUE) {
                    echo "<script>alert('You have successfully checked out at $checkOutTimeFormatted. Work Hours: $formattedWorkHours, Overtime: $formattedOvertimeHours');</script>";
                } else {
                    echo "<script>alert('Error updating work hours: " . $conn->error . "');</script>";
                }
            }
        } else {
            echo "<script>alert('Error checking out: " . $conn->error . "');</script>";
        }
    }
}

// Query to check for the previous entry for the employee on the current date
$checkPreviousEntryQuery = "SELECT atlog.atlog_id, atlog.check_in, atlog.check_out, atlog.status,
                            employee.shift, employee.emp_id, employee.contract, employee.first_name, 
                            employee.middle_name, employee.last_name
                            FROM atlog 
                            JOIN employee ON atlog.emp_id = employee.emp_id
                            WHERE atlog.emp_id = $empId AND atlog.atlog_date = CURDATE()";

$result = $conn->query($checkPreviousEntryQuery);

if ($result->num_rows > 0) {
    // Fetch the existing attendance log data
    $row = $result->fetch_assoc();
    $employeeFullName = $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'];
    $employeeShift = $row['shift'];
    $employeeContract = $row['contract'];
    $atlogId = $row['atlog_id']; // Store atlog_id for future updates
    $checkIn = $row['check_in'];  // Assign check_in value
    $checkOut = $row['check_out'];  // Assign check_out value
    $status = $row['status']; // Get status value (Online/Offline)
} else {
    // If no entry for today, fetch the employee details
    $getEmployeeNameQuery = "SELECT first_name, middle_name, last_name, shift, contract FROM employee WHERE emp_id = $empId";
    $employeeNameResult = $conn->query($getEmployeeNameQuery);

    if ($employeeNameResult->num_rows > 0) {
        // Fetch employee data
        $row = $employeeNameResult->fetch_assoc();
        $employeeFullName = $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'];
        $employeeShift = $row['shift'];
        $employeeContract = $row['contract'];

        // Create a new entry in the attendance log for today if not found
        $createNewEntryQuery = "INSERT INTO atlog (emp_id, atlog_date) VALUES ($empId, CURDATE())";
        if ($conn->query($createNewEntryQuery) === TRUE) {
            // Fetch new entries for today
            $getNewEntries = "SELECT atlog.atlog_id, atlog.check_in, atlog.check_out, atlog.status,
                              employee.emp_id, employee.first_name, employee.middle_name, employee.last_name
                              FROM atlog 
                              JOIN employee ON atlog.emp_id = employee.emp_id
                              WHERE atlog.emp_id = $empId AND atlog.atlog_date = CURDATE()";
            $resultNew = $conn->query($getNewEntries);
            $row = $resultNew->fetch_assoc();
            $atlogId = $row['atlog_id']; // Store the new atlog_id
            $checkIn = $row['check_in']; // Assign check_in value
            $checkOut = $row['check_out']; // Assign check_out value
            $status = $row['status']; // Get the status value
        }
    }
}

// Close the database connection
$conn->close();

// Function to convert time to decimal
function convertTimeToDecimal($time) {
    if (!empty($time)) {
        $timeParts = explode(":", $time);
        if (count($timeParts) === 3) {
            $hours = (int)$timeParts[0];
            $minutes = (int)$timeParts[1];
            return $hours + ($minutes / 60);
        }
    }
    return 0; // Return 0 if invalid or empty time format
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Log in</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/employee-attendance-system.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" defer></script>
    <script src="../js/nav-bar.js" defer></script>
    <script src="../js/date-time.js" defer></script>
</head>
<body class="container-fluid">
    <div class="container-fluid row gap-0">
        <!-- Include the navbar -->
        <?php include_once __DIR__ . '/../php/employee-navbar.php'; ?>

        <div class="right_panel container p-5">
            <p class="header_title">Welcome <span class="employee_name" id="employee_name"><?php echo $employeeFullName; ?></span>!</p>
            <div class="container-fluid mt-4 row gap-3">
                <div class="col col-8 p-0">
                    <div class="row m-0 gap-3">
                        <div class="date_container card px-4 py-2 col col-6">
                            <p class="date_subtitle m-0 p-0">Today is <span class="day_title" id="day_today"></span></p>
                            <span class="date_title"><p class="mb-0 p-0" id="full_date"></p></span>
                        </div>                       

                        <div class="clock_container grey_container col col-5 ms-auto p-0">
                            <div class="clock_elements">
                                <span id="hour"></span>
                                <span id="point">:</span>
                                <span id="minute"></span>
                                <span id="point">:</span>
                                <span id="second"></span>
                                <span id="am_pm"></span>
                            </div>
                        </div>
                    </div>

                    <div class="grey_container row mt-3 p-4 mx-0 text-center justify-content-evenly">
                        <div class="col col-5 card p-3">
                            <div class="am_title container w-50 mb-3 rounded-2 p-2"><p class="m-0">Check In</p></div>
                            <form class="in_out_button row gap-2 m-0 p-0 justify-content-center" method="POST">
                                <?php
                                    if ($employeeShift === 'Morning Shift' || $employeeShift === 'Day Shift') {
                                        // Display IN button for AM shift
                                        if ($checkIn === null) {
                                            echo '<input type="hidden" name="action" value="check_in">'; // Hidden input to specify the action
                                            echo '<input type="hidden" name="atlog_id" value="' . $atlogId . '">'; // Send atlog_id for reference
                                            echo '<button class="btn in_button col col-auto" type="submit">Check In</button>';
                                        } else {
                                            echo '<button class="btn in_button col col-auto" disabled>Checked In</button>';
                                        }
                                    }
                                ?>
                            </form>
                        </div>

                        <div class="col col-5 card p-3">
                            <div class="pm_title container w-50 mb-3 rounded-2 p-2"><p class="m-0">Check Out</p></div>
                            <form class="in_out_button row gap-2 m-0 p-0 justify-content-center" method="POST">
                                <?php
                                    // Show check-out button if employee has checked in and has not yet checked out
                                    if ($checkIn !== null && $checkOut === null) {
                                        echo '<input type="hidden" name="action" value="check_out">'; // Hidden input to specify the action
                                        echo '<input type="hidden" name="atlog_id" value="' . $atlogId . '">'; // Send atlog_id for reference
                                        echo '<button class="btn out_button col col-auto" type="submit">Check Out</button>';
                                    } else {
                                        echo '<button class="btn out_button col col-auto" disabled>Checked Out</button>';
                                    }
                                ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col col-3-sm p-0">
                    <div class="grey_container p-3">
                        <div class="text-center emp_title mt-2 mb-3">Employee Information</div>
                        <form action="" class="card m-3 p-3 employee_info d-flex gap-2 flex-column justify-content-center align-items-center">
                            <div class="mb-2">Name: <?php echo $employeeFullName; ?></div>
                            <div class="mb-2">Shift: <?php echo $employeeShift; ?></div>
                            <div class="mb-2">Contract: <?php echo $employeeContract; ?></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
