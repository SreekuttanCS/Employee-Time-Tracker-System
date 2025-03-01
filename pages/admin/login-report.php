<?php
// Start the session
session_start();
include_once __DIR__ . '/../php/connection.php';

// Check if admin_id is set in the session
if (isset($_SESSION['admin_id'])) {
    $adminId = $_SESSION['admin_id'];

    $adminUsernameQuery = "SELECT username FROM admin WHERE admin_id = $adminId";

    $adminResult = $conn->query($adminUsernameQuery);

    // Check if admin entry is found
    if ($adminResult->num_rows > 0) {
        $adminRow = $adminResult->fetch_assoc();
        $adminUsername = $adminRow['username'];
    } else {
        echo '<script>console.log("No admin entry found");</script>';
        $adminUsername = '';
    }
} else {
    $adminUsername = '';
}

// Query to get the count of online employees
$sql = "SELECT COUNT(*) AS online_employees_count 
FROM atlog 
WHERE status = 'online'";

$result = mysqli_query($conn, $sql);

// Check if the query was successful
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $onlineEmployeesCount = $row['online_employees_count'];
} else {
    $onlineEmployeesCount = 0; // Default value if there's an error
}

// SQL query to fetch attendance data for today's date
$sql = "SELECT 
            atlog.emp_id, 
            employee.contract, 
            employee.shift, 
            employee.first_name, 
            employee.middle_name, 
            employee.last_name, 
            atlog.check_in, 
            atlog.check_out, 
            atlog.work_hour, 
            atlog.overtime_hour, 
            employee.total_work_hours,
            atlog.status,
            atlog.late_hours
        FROM atlog 
        JOIN employee ON atlog.emp_id = employee.emp_id
        WHERE atlog.atlog_DATE = CURDATE()";

$result = mysqli_query($conn, $sql);
$result_check = mysqli_num_rows($result);

$conn->close();

// Function to convert decimal hours to HH:MM:SS format
function convertToTimeFormat($decimalHours) {
    if (!is_numeric($decimalHours)) return '00:00:00'; // handle invalid value

    $hours = floor($decimalHours);
    $minutes = floor(($decimalHours - $hours) * 60);
    $seconds = floor(($decimalHours - $hours - ($minutes / 60)) * 3600);

    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}

// Function to calculate overtime (if any)
function calculateOvertime($workHour, $totalWorkHours) {
    // If workHour exceeds totalWorkHours, calculate overtime
    if ($workHour > $totalWorkHours) {
        // Calculate the overtime (difference between work hours and total work hours)
        $overtimeDuration = $workHour - $totalWorkHours;

        // Convert the overtime into hours, minutes, and seconds
        $overtimeHours = floor($overtimeDuration);
        $overtimeMinutes = floor(($overtimeDuration - $overtimeHours) * 60);
        $overtimeSeconds = floor(($overtimeDuration - $overtimeHours - ($overtimeMinutes / 60)) * 3600);

        return sprintf('%02d:%02d:%02d', $overtimeHours, $overtimeMinutes, $overtimeSeconds);
    }
    // If no overtime, return a dash
    return "-";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/nav-bar.css">
    <link rel="stylesheet" href="../css/login-report.css">
    <script src="../js/nav-bar.js" defer></script>
    <script src="../js/date-time.js" defer></script>
</head>
<body class="container-fluid">
    <div class="container-fluid row gap-0">
        <?php 
            include('../php/nav-bar.php');
        ?> 
        <!-- Main contents -->
        <div class="right_panel container p-5">
            <!-- Name must be according to id inputted by admin -->
            <div class="row">
                <div class="col col-9">
                    <p class="header_title">Welcome <span class="admin_name" id="admin_name"><?php echo htmlspecialchars($adminUsername); ?></span>!</p>
                </div>
                <div class="col col-auto">
                    <input id="exportButton" class="btn btn-secondary m-0" disabled value="Online Employees: <?php echo $onlineEmployeesCount; ?>"></input>
                </div>
            </div>
            
            <div class="row container-fluid m-0 gap-3">
                <!-- Date today -->
                <div class="date_container card px-4 py-2 col col-4 justify-content-center">
                    <p class="date_subtitle m-0 p-0">Today is <span class="day_title" id="day_today"></span></p>
                    <span class="date_title"><p class="mb-0 p-0" id="full_date"></p></span>
                </div>
                <!-- Real-time clock -->
                <div class="clock_container grey_container col col-4 m-0 p-0 ms-auto">
                    <div class="clock_elements">
                        <span id="hour"></span>
                        <span id="point">:</span>
                        <span id="minute"></span>
                        <span id="point">:</span>
                        <span id="second"></span>
                        <span id="am_pm"></span>
                    </div>
                </div>
                <!-- Table legend -->
                <div class="white_container col col-2 m-0 py-3 px-4">
                    <p class="legend_title text-center">Table legend</p>
                    <div class="legend_red"><i class="bi bi-square-fill"></i><span class="mx-1">Late</span></div>
                    <div class="legend_blue"><i class="bi bi-square-fill"></i><span class="mx-1">Undertime</span></div>
                    <div class="legend_green"><i class="bi bi-square-fill"></i><span class="mx-1">Overtime</span></div>
                </div>
            </div>

            <div class="white_container row mt-3 p-4 mx-0 text-center justify-content-evenly">
                <table class="table m-0 p-0">
                    <?php
                        if ($result_check > 0) {
                            echo "
                            <thead>
                                <tr>
                                    <th scope='col'>Emp ID</th>
                                    <th scope='col'>Full Name</th>
                                    <th scope='col'>Contract</th>
                                    <th scope='col'>Shift</th>
                                    <th scope='col'>Check In</th>
                                    <th scope='col'>Check Out</th>
                                    <th scope='col'>Work Hours</th>
                                    <th scope='col'>Overtime</th>
                                    <th scope='col'>Total Work Hours</th>
                                    <th scope='col'>Status</th>
                                    <th scope='col'>Late Hours</th>
                                </tr>
                            </thead>
                            <tbody class='table_body'>
                            ";
                            while ($row = mysqli_fetch_assoc($result)){
                                // Initialize variables for work hours and overtime
                                $workHour = 0;
                                $overtime = "-";  // Default overtime as dash
                                $lateHours = 0;

                                // Check if both check-in and check-out times exist
                                if ($row['check_in'] && $row['check_out']) {
                                    // Convert check-in and check-out times to DateTime objects
                                    $checkInTime = new DateTime($row['check_in']);
                                    $checkOutTime = new DateTime($row['check_out']);

                                    // Calculate the work duration between check-in and check-out
                                    $workDuration = $checkInTime->diff($checkOutTime);

                                    // Calculate work hours as decimal (total hours and minutes)
                                    $workHour = $workDuration->h + ($workDuration->i / 60);  // works in hours (decimal)
                                }

                                // Ensure total_work_hours is a valid numeric value (i.e., integer or float)
                                $totalWorkHours = is_numeric($row['total_work_hours']) ? floatval($row['total_work_hours']) : 0;

                                // Calculate overtime using the function
                                $overtime = calculateOvertime($workHour, $totalWorkHours);

                                // Display the row with the calculated values
                                echo "<tr>";
                                echo "<td>" . $row["emp_id"] . "</td>";
                                echo "<td>" . $row["first_name"] . " " . $row["middle_name"] . " " . $row["last_name"] . "</td>";
                                echo "<td>" . $row["contract"] . "</td>";
                                echo "<td>" . $row["shift"] . "</td>";
                                echo "<td>" . ($row["check_in"] ? $row["check_in"] : '-') . "</td>";
                                echo "<td>" . ($row["check_out"] ? $row["check_out"] : '-') . "</td>";
                                echo "<td>" . ($workHour ? convertToTimeFormat($workHour) : '-') . "</td>";
                                echo "<td>" . $overtime . "</td>";  // Show overtime or dash if no overtime
                                echo "<td>" . ($row["total_work_hours"] ? $row["total_work_hours"] : '-') . "</td>";
                                echo "<td>" . ($row["status"] ? $row["status"] : '-') . "</td>";
                                echo "<td>" . ($lateHours ? convertToTimeFormat($lateHours) : '-') . "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                        } else {
                            echo "<tr><td colspan='11'>No data available for today.</td></tr>";
                        }
                    ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
                        