<?php 
include_once __DIR__ . '/../php/connection.php';

// Function to generate the table row
function generateTableRow($row) {
    $tableRow = "<tr>";
    // Emp ID
    $tableRow .= "<td>" . $row["emp_id"] . "</td>";

    // Full Name (assuming you want to join employee table for full name)
    $tableRow .= "<td>" . $row["first_name"] . " " . $row["middle_name"] . " " . $row["last_name"] . "</td>";

    // Contract and Shift (assuming these fields exist in the employee table)
    $tableRow .= "<td>" . $row["contract"] . "</td>";
    $tableRow .= "<td>" . $row["shift"] . "</td>";

    // Check In
    $checkIn = isset($row["check_in"]) ? $row["check_in"] : "-";
    $tableRow .= "<td>" . $checkIn . "</td>";

    // Check Out
    $checkOut = isset($row["check_out"]) ? $row["check_out"] : "-";
    $tableRow .= "<td>" . $checkOut . "</td>";

    // Work Hours
    $tableRow .= "<td>";
    if ($row["work_hour"] > "00:00:00") {
        $tableRow .= "<span style='color:green'>" . $row["work_hour"] . "</span>";
    } else {
        $tableRow .= $row["work_hour"];
    }
    $tableRow .= "</td>";

    // Overtime Hours
    $tableRow .= "<td>";
    if ($row["overtime_hour"] > "00:00:00") {
        $tableRow .= "<span style='color:green'>" . $row["overtime_hour"] . "</span>";
    } else {
        $tableRow .= $row["overtime_hour"];
    }
    $tableRow .= "</td>";

    // Status
    $tableRow .= "<td>" . $row["status"] . "</td>";

    // Late Hours
    $tableRow .= "<td>";
    if ($row["late_hours"] > "00:00:00") {
        $tableRow .= "<span style='color:red'>" . $row["late_hours"] . "</span>";
    } else {
        $tableRow .= $row["late_hours"];
    }
    $tableRow .= "</td>";

    $tableRow .= "</tr>";

    return $tableRow;
}

// For displaying reports for the current month
if (isset($_POST['table_onload'])) {
    $date_picked = ($_POST['table_onload']);
    
    // Using STR_TO_DATE to ensure the date is properly formatted
    $sql = "SELECT atlog.emp_id, atlog.work_hour, atlog.overtime_hour, atlog.atlog_DATE,
            employee.first_name, employee.last_name, employee.middle_name, employee.shift,
            employee.contract, atlog.check_in, atlog.check_out, employee.total_work_hours, atlog.status, atlog.late_hours
            FROM atlog
            JOIN employee ON atlog.emp_id = employee.emp_id
            WHERE MONTH(atlog.atlog_DATE) = MONTH(STR_TO_DATE('$date_picked', '%m/%d/%Y'))";
    
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<table class='table' id='table_rows'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th scope='col'>Emp ID</th>";
                    echo "<th scope='col'>Full Name</th>";
                    echo "<th scope='col'>Contract</th>";
                    echo "<th scope='col'>Shift</th>";
                    echo "<th scope='col'>Check In</th>";
                    echo "<th scope='col'>Check Out</th>";
                    echo "<th scope='col'>Work Hours</th>";
                    echo "<th scope='col'>Overtime Hours</th>";
                    echo "  <th scope='col'>Total Work Hour</th>";                    
                    echo "<th scope='col'>Status</th>";
                    echo "<th scope='col'>Late Hours</th>";
                echo "</tr>";
            echo "</thead>";

            echo "<tbody class='table_body' id='table_body'>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo generateTableRow($row);
            }
            echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='alert alert-danger m-0 p-3' role='alert'>No Records Found</div>";
    }   
}

// For a specific date
if (isset($_POST['select_date'])) {
    $selected_date = ($_POST['select_date']);
    list($year, $month) = explode('-', $selected_date);
    
    // Do not use substr() on year; use it directly
    $sql = "SELECT atlog.emp_id, atlog.work_hour, atlog.overtime_hour, atlog.atlog_DATE,
            employee.first_name, employee.last_name, employee.middle_name, employee.shift,
            employee.contract, atlog.check_in, atlog.check_out, atlog.status, atlog.late_hours
            FROM atlog
            JOIN employee ON atlog.emp_id = employee.emp_id
            WHERE MONTH(atlog.atlog_DATE) = '$month' AND YEAR(atlog.atlog_DATE) = '$year'";
    
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<table class='table' id='table_rows'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th scope='col'>Emp ID</th>";
                    echo "<th scope='col'>Full Name</th>";
                    echo "<th scope='col'>Contract</th>";
                    echo "<th scope='col'>Shift</th>";
                    echo "<th scope='col'>Check In</th>";
                    echo "<th scope='col'>Check Out</th>";
                    echo "<th scope='col'>Work Hours</th>";
                    echo "<th scope='col'>Overtime Hours</th>";
                    echo "<th scope='col'>Status</th>";
                    echo "<th scope='col'>Late Hours</th>";
                echo "</tr>";
            echo "</thead>";

            echo "<tbody class='table_body' id='table_body'>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo generateTableRow($row);
            }
            echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='alert alert-danger m-0 p-3' role='alert'>No Records Found</div>";
    }
}

// For a specific employee and date
if (isset($_POST['emp_id'])) {
    $emp_id = ($_POST['emp_id']);
    $selected_date = ($_POST['select_date']);
    list($year, $month) = explode('-', $selected_date);
    
    // Use the year and month directly
    $sql = "SELECT atlog.emp_id, atlog.work_hour, atlog.overtime_hour, atlog.atlog_DATE,
            employee.first_name, employee.last_name, employee.middle_name, employee.shift,
            employee.contract, atlog.check_in, atlog.check_out,  employee.total_work_hours,atlog.status, atlog.late_hours
            FROM atlog
            JOIN employee ON atlog.emp_id = employee.emp_id
            WHERE MONTH(atlog.atlog_DATE) = '$month' AND YEAR(atlog.atlog_DATE) = '$year' 
            AND atlog.emp_id = '$emp_id'";
    
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<table class='table' id='table_rows'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th scope='col'>Emp ID</th>";
                    echo "<th scope='col'>Full Name</th>";
                    echo "<th scope='col'>Contract</th>";
                    echo "<th scope='col'>Shift</th>";
                    echo "<th scope='col'>Check In</th>";
                    echo "<th scope='col'>Check Out</th>";
                    echo "<th scope='col'>Work Hours</th>";
                    echo "<th scope='col'>Overtime Hours</th>";
                     echo "<th scope='col'>Overtime Hours</th>";
                    echo "<th scope='col'>Status</th>";
                    echo "<th scope='col'>Late Hours</th>";
                echo "</tr>";
            echo "</thead>";

            echo "<tbody class='table_body' id='table_body'>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo generateTableRow($row);
            }
            echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='alert alert-danger m-0 p-3' role='alert'>No Records Found</div>";
    }
}
?>
