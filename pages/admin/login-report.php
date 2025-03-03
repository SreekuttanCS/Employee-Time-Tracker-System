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
                atlog.status
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
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
                        <div class="legend_blue"><i class="bi bi-square-fill"></i><span class="mx-1">Online</span></div>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="table-container mt-4 table-responsive">
                    <table class="table table-bordered table-striped" id="attendance-table">
                        <thead>
                            <tr>
                                <th>Emp ID</th>
                                <th>Full Name</th>
                                <th>Contract</th>
                                <th>Shift</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Work Hours</th>
                                <th>Overtime</th>
                                <th>Total Work Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_check > 0) : ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <tr>
                                        <td class="text-center"><?php echo $row['emp_id']; ?></td>
                                        <td><?php echo $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']; ?></td>
                                        <td><?php echo $row['contract']; ?></td>
                                        <td><?php echo $row['shift']; ?></td>
                                        <td><?php echo $row['check_in']; ?></td>
                                        <td><?php echo $row['check_out']; ?></td>
                                        <td><?php echo convertToTimeFormat($row['work_hour']); ?></td>
                                        <td><?php echo calculateOvertime($row['work_hour'], $row['total_work_hours']); ?></td>
                                        <td><?php echo $row['total_work_hours']; ?></td>
                                        <td class="text-center">
                                            <span class="status <?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
    </html>
