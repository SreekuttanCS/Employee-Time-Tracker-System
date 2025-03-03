<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">    
    <link rel="stylesheet" href="../css/monthly-report.css">
    <link rel="stylesheet" href="../css/nav-bar.css">
    
    <!-- Include jsPDF (UMD version) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../js/nav-bar.js" defer></script>
    <script src="../js/date-time.js" defer></script>
    <script src="../js/month-year-calendar.js" defer></script>
</head>
<body class="container-fluid">
    <div class="container-fluid row gap-0">
        <!-- Include your navigation bar -->
        <?php include('../php/nav-bar.php'); ?>

        <!-- Calendar modal -->
        <div class="modal fade" id="calendar_modal" tabindex="-1" aria-labelledby="calendar_label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="calendar_label">Date picker</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Select the month and year</p>
                        <input class="form-control" type="month" name="date_picker" id="picked_date">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="close_button btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="search_button">Search</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main contents -->
        <div class="right_panel container p-5">
            <div class="header row container-fluid align-items-center m-0 p-0 gap-2">
                <div class="col col-10 m-0 p-0"><p class="header_title"><span class="blue_title">Monthly Log in</span> Report</p></div>
                <!-- Export button -->
                <div class="col col-auto m-0 p-0 ms-auto export_button">
                    <button id="exportButton" class="btn btn-secondary m-0">Export to PDF</button>
                </div>
                <!-- Refresh button -->
                <div class="refresh_button col col-auto p-0">
                    <a href="monthly-report.php"><button type="button" class="btn btn-primary w-100"><i class="bi bi-arrow-clockwise"></i></button></a>
                </div>
            </div>

            <!-- First row -->
            <div class="row container-fluid mt-2 gap-3 d-flex">
                <div class="date_container card px-4 py-2 col col-4 justify-content-center" style="display: none;" id="date_picked">
                    <p class="date_subtitle">Viewing log in reports during</p>
                    <p class="date_title" id="selected_date"></p>
                </div>
                <div class="date_container card px-4 py-2 col col-4 justify-content-center" id="current_date">
                    <p class="date_subtitle">Viewing log in reports today</p>
                    <p class="date_title" id="month_year"></p>
                </div>
                <div class="card col col-1 p-0 align-items-center justify-content-center">
                    <div class="calendar_icon"><a type="button" data-bs-toggle="modal" data-bs-target="#calendar_modal"><i class="bi bi-calendar4-week" style="font-size: 2rem;"></i></a></div>
                </div>
                <div class="clock_container grey_container col col-3 m-0 p-0 ms-auto">
                    <div class="clock_elements">
                        <span id="hour"></span>:<span id="minute"></span>:<span id="second"></span><span id="am_pm"></span>
                    </div>
                </div>
            </div>

            <div class="white_container row mt-3 p-4 mx-0 text-center justify-content-evenly" id="table_vals"></div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            const today = new Date().toLocaleDateString();
            const curr = new Date();
            const month = curr.getMonth() + 1; // Get the current month
            const year = curr.getFullYear(); // Get the current year
            const global_date_pick = `${year}-${month < 10 ? '0' + month : month}`; // Format current date

            // Display current month-year for reference
            $('#month_year').text(`${year}-${month < 10 ? '0' + month : month}`);

            // Load table values for the current date
            $("#table_vals").load("monthly-load.php", {
                table_onload: today,
            });

            // Calendar date picker functionality
            $("#search_button").click(function () {
                var date_pick = $('#picked_date').val();
                $("#table_vals").load("monthly-load.php", {
                    select_date: date_pick,
                });
                $("#selected_date").text(date_pick); // Display selected date
                $("#date_picked").show(); // Show the selected date section
                $("#current_date").hide(); // Hide current date section
            });

            // PDF Export Functionality
            document.getElementById('exportButton').addEventListener('click', function() {
                const selectedDate = document.getElementById('selected_date').innerText || ''; // Get the selected date
                const currentDate = document.getElementById('month_year').innerText; // Get the current date

                let fileName = selectedDate ? selectedDate : currentDate; // Default to current date if no selected date
                const { jsPDF } = window.jspdf; // Ensure jsPDF is correctly referenced

                const pdf = new jsPDF();
                const contentDiv = document.getElementById('table_vals');

                pdf.autoTable({
                    html: contentDiv,
                    startY: 30,
                    theme: 'grid',
                    tableWidth: 190, // Adjust as needed
                    margin: { top: 20, left: 0, right: 0, bottom: 20 }
                });

                pdf.text(`${fileName} Monthly Report`, 15, 15); // Add title
                pdf.save(`${fileName}.pdf`); // Save the PDF with the constructed file name
            });
        });
    </script>
</body>
</html>
