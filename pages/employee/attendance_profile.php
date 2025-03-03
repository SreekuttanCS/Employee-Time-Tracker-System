<?php
session_start();
include_once __DIR__ . '/../php/connection.php'; // Ensure the path is correct

$empId = $_SESSION['emp_id']; // Get employee ID from session

// Initialize variables for employee information
$employeeFullName = '';
$employeeShift = '';
$employeeContract = '';
$employeeEmail = '';
$employeeContactNumber = '';
$employeeAddress = '';

// Query to get employee profile information
$getEmployeeProfileQuery = "SELECT first_name, middle_name, last_name, shift, contract, email_address, contact_number, address FROM employee WHERE emp_id = $empId";
$employeeProfileResult = $conn->query($getEmployeeProfileQuery);

if ($employeeProfileResult->num_rows > 0) {
    // Fetch employee data
    $row = $employeeProfileResult->fetch_assoc();
    $employeeFullName = $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'];
    $employeeShift = $row['shift'];
    $employeeContract = $row['contract'];
    $employeeEmail = $row['email_address'];
    $employeeContactNumber = $row['contact_number'];
    $employeeAddress = $row['address'];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/nav-bar.css">
  <link rel="stylesheet" href="../css/employee-attendance-system.css">
  <script src="../js/nav-bar.js" defer></script>
</head>
<body class="container-fluid">
  	<div class="container-fluid row gap-0">
		<!-- Include the navbar -->
		<?php include_once __DIR__ . '/../php/employee-navbar.php'; ?>
		
		<!-- Main contents -->
		<div class="right_panel container p-5">
			<div class="header row container-fluid align-items-top">
				<div class="col col-10 m-0 p-0"><p class="header_title">Employee <span class="blue_title">Profile</span></p></div>
				<div class="col col-auto m-0 p-0 ms-auto exit_button">
					<a href="employee-attendance-system.php" class="nav-link"><button type="button" class="btn btn-secondary py-2 px-3">Go Back</button></a>
				</div>
			</div>

			<div class="container-fluid gap-3">
				<div class="row p-0 mx-0 justify-content-evenly">
					<!-- Profile Display Form -->
					<div class="card py-5 px-4">
						<form class="profile_form row g-3">
							<div class="col-md-6">
								<label for="inputFirstName" class="form-label">Full Name</label>
								<input type="text" class="form-control" id="input-full-name" value="<?php echo $employeeFullName ?>" disabled>
							</div>
							<div class="col-md-6">
								<label for="inputEmail" class="form-label">Email Address</label>
								<input type="email" class="form-control" id="input-email" value="<?php echo $employeeEmail ?>" disabled>
							</div>
							
							<div class="col-md-6">
								<label for="inputShift" class="form-label">Shift</label>
								<input type="text" class="form-control" id="input-shift" value="<?php echo $employeeShift ?>" disabled>
							</div>
							
							<div class="col-md-6">
								<label for="inputContract" class="form-label">Contract</label>
								<input type="text" class="form-control" id="input-contract" value="<?php echo $employeeContract ?>" disabled>
							</div>

							<div class="col-md-6">
								<label for="inputContactNumber" class="form-label">Contact Number</label>
								<input type="text" class="form-control" id="input-contact-number" value="<?php echo $employeeContactNumber ?>" disabled>
							</div>

							<div class="col-md-6">
								<label for="inputAddress" class="form-label">Address</label>
								<input type="text" class="form-control" id="input-address" value="<?php echo $employeeAddress ?>" disabled>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
