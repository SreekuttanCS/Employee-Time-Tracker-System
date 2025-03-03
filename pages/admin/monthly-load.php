<?php
// monthly-load.php

// Include database connection
include_once __DIR__ . '/../php/connection.php';

// Function to generate the table row
function generateTableRow($row) {
    $tableRow = "<tr>";
    
    // Emp ID
    $tableRow .= "<td>" . $row["emp_id"] . "</td>";

    // Full Name
    $tableRow .= "<td>" . $row["first_name"] . " " . $row["middle_name"] . " " . $row["last_name"] . "</td>";

    // Contract and Shift
    $tableRow .= "<td>" . $row["contract"] . "</td>";
    $tableRow .= "<td>" . $row["shift"] . "</td>";

    // Date (Display only the date part)
    $date = isset($row["atlog_DATE"]) ? date("Y-m-d", strtotime($row["atlog_DATE"])) : "-"; // Formatting the date
    $tableRow .= "<td>" . $date . "</td>";

    // Check In (Only Time)
    $checkInTime = isset($row["check_in"]) ? $row["check_in_time"] : "-";
    $tableRow .= "<td>" . $checkInTime . "</td>";

    // Check Out (Only Time)
    $checkOutTime = isset($row["check_out"]) ? $row["check_out_time"] : "-";
    $tableRow .= "<td>" . $checkOutTime . "</td>";

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

    $tableRow .= "</tr>";

    return $tableRow;
}

// For displaying reports for the current month
if (isset($_POST['table_onload'])) {
    $date_picked = $_POST['table_onload'];

    // Prepare the SQL query for the selected month and year
    $sql = "SELECT atlog.emp_id, 
                    atlog.work_hour, 
                    atlog.overtime_hour, 
                    atlog.atlog_DATE,
                    employee.first_name, 
                    employee.last_name, 
                    employee.middle_name, 
                    employee.shift,
                    employee.contract, 
                    atlog.check_in, 
                    atlog.check_out, 
                    TIME(atlog.check_in) AS check_in_time,
                    TIME(atlog.check_out) AS check_out_time,
                    TIMEDIFF(atlog.check_out, atlog.check_in) AS work_hour -- Work hour calculation
            FROM atlog
            JOIN employee ON atlog.emp_id = employee.emp_id
            WHERE MONTH(atlog.atlog_DATE) = MONTH(STR_TO_DATE(?, '%m/%d/%Y'))
            ORDER BY atlog.atlog_DATE DESC"; // Sorting by atlog_DATE in descending order

    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameter to the prepared statement
        $stmt->bind_param("s", $date_picked);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table class='table' id='table_rows'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th scope='col'>Emp ID</th>";
            echo "<th scope='col'>Full Name</th>";
            echo "<th scope='col'>Contract</th>";
            echo "<th scope='col'>Shift</th>";
            echo "<th scope='col'>Date</th>";  // Displaying atlog_DATE here
            echo "<th scope='col'>Check In</th>";
            echo "<th scope='col'>Check Out</th>";
            echo "<th scope='col'>Work Hours</th>";
            echo "<th scope='col'>Overtime Hours</th>";
            echo "</tr>";
            echo "</thead>";

            echo "<tbody class='table_body' id='table_body'>";
            while ($row = $result->fetch_assoc()) {
                echo generateTableRow($row);
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<div class='alert alert-danger m-0 p-3' role='alert'>No Records Found</div>";
        }

        $stmt->close();
    }
}

// For a specific date
if (isset($_POST['select_date'])) {
    $selected_date = $_POST['select_date'];
    list($year, $month) = explode('-', $selected_date);

    $sql = "SELECT atlog.emp_id, 
                    atlog.work_hour, 
                    atlog.overtime_hour, 
                    atlog.atlog_DATE,
                    employee.first_name, 
                    employee.last_name, 
                    employee.middle_name, 
                    employee.shift,
                    employee.contract, 
                    atlog.check_in, 
                    atlog.check_out, 
                    TIME(atlog.check_in) AS check_in_time,
                    TIME(atlog.check_out) AS check_out_time,
                    TIMEDIFF(atlog.check_out, atlog.check_in) AS work_hour -- Work hour calculation
            FROM atlog
            JOIN employee ON atlog.emp_id = employee.emp_id
            WHERE MONTH(atlog.atlog_DATE) = ? AND YEAR(atlog.atlog_DATE) = ?
            ORDER BY atlog.atlog_DATE DESC"; // Sorting by atlog_DATE in descending order

    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters for the month and year
        $stmt->bind_param("ii", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table class='table' id='table_rows'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th scope='col'>Emp ID</th>";
            echo "<th scope='col'>Full Name</th>";
            echo "<th scope='col'>Contract</th>";
            echo "<th scope='col'>Shift</th>";
            echo "<th scope='col'>Date</th>";  // Displaying atlog_DATE here
            echo "<th scope='col'>Check In</th>";
            echo "<th scope='col'>Check Out</th>";
            echo "<th scope='col'>Work Hours</th>";
            echo "<th scope='col'>Overtime Hours</th>";
            echo "</tr>";
            echo "</thead>";

            echo "<tbody class='table_body' id='table_body'>";
            while ($row = $result->fetch_assoc()) {
                echo generateTableRow($row);
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<div class='alert alert-danger m-0 p-3' role='alert'>No Records Found</div>";
        }

        $stmt->close();
    }
}

// For a specific employee and date
if (isset($_POST['emp_id'])) {
    $emp_id = $_POST['emp_id'];
    $selected_date = $_POST['select_date'];
    list($year, $month) = explode('-', $selected_date);

    $sql = "SELECT atlog.emp_id, 
                    atlog.work_hour, 
                    atlog.overtime_hour, 
                    atlog.atlog_DATE,
                    employee.first_name, 
                    employee.last_name, 
                    employee.middle_name, 
                    employee.shift,
                    employee.contract, 
                    atlog.check_in, 
                    atlog.check_out, 
                    TIME(atlog.check_in) AS check_in_time,
                    TIME(atlog.check_out) AS check_out_time,
                    TIMEDIFF(atlog.check_out, atlog.check_in) AS work_hour -- Work hour calculation
            FROM atlog
            JOIN employee ON atlog.emp_id = employee.emp_id
            WHERE MONTH(atlog.atlog_DATE) = ? AND YEAR(atlog.atlog_DATE) = ? 
            AND atlog.emp_id = ?
            ORDER BY atlog.atlog_DATE DESC"; // Sorting by atlog_DATE in descending order

    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters for the month, year, and employee ID
        $stmt->bind_param("iii", $month, $year, $emp_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table class='table' id='table_rows'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th scope='col'>Emp ID</th>";
            echo "<th scope='col'>Full Name</th>";
            echo "<th scope='col'>Contract</th>";
            echo "<th scope='col'>Shift</th>";
            echo "<th scope='col'>Date</th>";  // Displaying atlog_DATE here
            echo "<th scope='col'>Check In</th>";
            echo "<th scope='col'>Check Out</th>";
            echo "<th scope='col'>Work Hours</th>";
            echo "<th scope='col'>Overtime Hours</th>";
            echo "</tr>";
            echo "</thead>";

            echo "<tbody class='table_body' id='table_body'>";
            while ($row = $result->fetch_assoc()) {
                echo generateTableRow($row);
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<div class='alert alert-danger m-0 p-3' role='alert'>No Records Found</div>";
        }

        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>
