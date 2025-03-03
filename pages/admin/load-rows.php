<?php
// Include the database connection file
include_once __DIR__ . '/../php/connection.php';

// Get the selected date from the POST request and sanitize it
if (isset($_POST['table_date'])) {
    $date_picked = mysqli_real_escape_string($conn, $_POST['table_date']);
} else {
    $date_picked = date('Y-m-d');  // Default to today's date if no date is picked
}

// Prepare the SQL statement
$stmt = $conn->prepare("SELECT 
        atlog.emp_id, 
        CONCAT(employee.first_name, ' ', employee.middle_name, ' ', employee.last_name) AS full_name, 
        employee.contract, 
        employee.shift, 
        atlog.check_in, 
        atlog.check_out, 
        employee.total_work_hours,
        atlog.work_hour, 
        atlog.overtime_hour
    FROM 
        atlog
    JOIN 
        employee ON atlog.emp_id = employee.emp_id
    WHERE 
        atlog.atlog_date = ?");

// Bind the date parameter
$stmt->bind_param("s", $date_picked); // "s" denotes that the parameter is a string

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if any records were returned
if ($result->num_rows > 0) {
    // Output the table headers without Status and Late Hours
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
        </tr>
    </thead>
    <tbody>";

    // Fetch and display each record without Status and Late Hours
    while ($row = $result->fetch_assoc()) {
        // Use the pre-calculated work hours and overtime from the database
        $work_hours = $row['work_hour'];
        $overtime_hours = $row['overtime_hour'];

        echo "<tr>";
        echo "<td>" . $row["emp_id"] . "</td>";
        echo "<td>" . $row["full_name"] . "</td>";
        echo "<td>" . $row["contract"] . "</td>";
        echo "<td>" . $row["shift"] . "</td>";
        echo "<td>" . ($row["check_in"] ? $row["check_in"] : "Not Recorded") . "</td>";
        echo "<td>" . ($row["check_out"] ? $row["check_out"] : "Not Recorded") . "</td>";
        echo "<td>" . number_format($work_hours, 2) . "</td>";
        echo "<td>" . number_format($overtime_hours, 2) . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
} else {
    echo "<tr><td colspan='8' class='text-center'>No Records Found for the selected date: $date_picked</td></tr>";
}
?>
