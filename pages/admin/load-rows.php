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
    // Output the table headers
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

    // Fetch and display each record
    while ($row = $result->fetch_assoc()) {
        $work_hours = $row['work_hour'] ?: ''; // Display as blank if not recorded
        $overtime_hours = $row['overtime_hour'] ?: ''; // Display as blank if not recorded

        $check_in = $row["check_in"] ?: ''; // Display as blank if not recorded
        $check_out = $row["check_out"] ?: ''; // Display as blank if not recorded

        echo "<tr>";
        echo "<td>" . $row["emp_id"] . "</td>";
        echo "<td>" . $row["full_name"] . "</td>";
        echo "<td>" . $row["contract"] . "</td>";
        echo "<td>" . $row["shift"] . "</td>";
        echo "<td>" . $check_in . "</td>";
        echo "<td>" . $check_out . "</td>";
        echo "<td>" . $work_hours . "</td>";
        echo "<td>" . $overtime_hours . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
} else {
    echo "<tr><td colspan='8' class='text-center'>No Records Found for the selected date: <strong>$date_picked</strong></td></tr>";
}
?>
