<?php
    include_once __DIR__ . '/../php/update.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">    
    <link rel="stylesheet" href="../css/daily-report.css">
    <link rel="stylesheet" href="../css/nav-bar.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../js/nav-bar.js" defer></script>
    <script src="../js/date-time.js" defer></script>
    <script src="../js/full-calendar.js" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var today = new Date().toISOString().slice(0, 10); // Current date in yyyy-mm-dd format
            $("#table_main").load("load-rows.php", {
                table_date: today
            });

            $("#search_button").click(function() {
                var date_picked = document.getElementById('picked_date').value;
                $("#table_main").load("load-rows.php", {
                    table_date: date_picked
                });
                $("#selected_date").text(date_picked);
                $("#current_date").hide();
                $("#date_picked").show();
            });

            document.getElementById('exportButton').addEventListener('click', function() {
                const selectedDate = document.getElementById('selected_date').innerHTML;
                const currentDate = document.getElementById('full_date').innerHTML;
                let fileName = 'daily_report';
                let finalDate = selectedDate || currentDate;

                const pdf = new jsPDF();
                const contentDiv = document.getElementById('table_main');

                const columnWidths = [20, 20, 20, 20, 20, 20, 20, 20]; // Define column widths for 8 columns
                pdf.autoTable({
                    html: contentDiv,
                    startX: 0,
                    startY: 30,
                    theme: 'grid',
                    columnStyles: {
                        0: { cellWidth: columnWidths[0], halign: 'center' },
                        1: { cellWidth: columnWidths[1], halign: 'center' },
                        2: { cellWidth: columnWidths[2], halign: 'center' },
                        3: { cellWidth: columnWidths[3], halign: 'center' },
                        4: { cellWidth: columnWidths[4], halign: 'center' },
                        5: { cellWidth: columnWidths[5], halign: 'center' },
                        6: { cellWidth: columnWidths[6], halign: 'center' },
                        7: { cellWidth: columnWidths[7], halign: 'center' }
                    },
                    headerStyles: {
                        fillColor: [7, 37, 96],
                        textColor: [255, 255, 255],
                        halign: 'center' // Center align for header cells
                    },
                    columns: [
                        { title: "Emp ID", dataKey: "emp_id" },
                        { title: "Full Name", dataKey: "full_name" },
                        { title: "Contract", dataKey: "contract" },
                        { title: "Shift", dataKey: "shift" },
                        { title: "Check In", dataKey: "check_in" },
                        { title: "Check Out", dataKey: "check_out" },
                        { title: "Work Hours", dataKey: "work_hours" },
                        { title: "Overtime", dataKey: "overtime" }
                    ]
                });

                pdf.text(finalDate + ' Daily Report', 15, 15);
                pdf.save(fileName + '.pdf');
            });
        });
    </script>
</head>
<body class="container-fluid">
    <div class="container-fluid row gap-0">
        <?php 
            include('../php/nav-bar.php');
        ?>
        <div class="modal fade" id="calendar_modal" tabindex="-1" aria-labelledby="calendar_label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="calendar_label">Date picker</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Select a date</p>
                        <input class="form-control" type="date" name="date_picker" id="picked_date">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="search_button">Search</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="right_panel container p-5">
            <div class="header row container-fluid align-items-center m-0 p-0 gap-2">
                <div class="col col-10 m-0 p-0"><p class="header_title"><span class="blue_title">Daily Log in</span> Report</p></div>
                <div class="col col-auto m-0 p-0 ms-auto export_button">
                    <button id="exportButton" class="btn btn-secondary m-0">Export to PDF</button>
                </div>
            </div>

            <div class="row container-fluid mt-2 gap-3 d-flex">
                <div class="date_container card px-4 py-2 col col-4 justify-content-center" style="display: none;" id="date_picked">
                    <p class="date_subtitle">Viewing log in reports during</p>
                    <p class="date_title" id="selected_date"></p>
                </div>
                <div class="date_container card px-4 py-2 col col-4 justify-content-center" id="current_date">
                    <p class="date_subtitle">Viewing log in reports today</p>
                    <p class="date_title" id="full_date"></p>
                </div>
                <div class="card col col-1 p-0 align-items-center justify-content-center">
                    <div class="calendar_icon">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#calendar_modal"><i class="bi bi-calendar4-week" style="font-size: 2rem;"></i></a>
                    </div>
                </div>

                <div class="clock_container grey_container col col-3 m-0 p-0 ms-auto">
                    <div class="clock_elements">
                        <span id="hour"></span>
                        <span id="point">:</span>
                        <span id="minute"></span>
                        <span id="point">:</span>
                        <span id="second"></span>
                        <span id="am_pm"></span>
                    </div>
                </div>

                <div class="white_container col col-2 m-0 py-3 px-4">
                    <p class="legend_title text-center">Table legend</p>
                    <div class="legend_red"><i class="bi bi-square-fill"></i><span class="mx-1">Late</span></div>
                    <div class="legend_blue"><i class="bi bi-square-fill"></i><span class="mx-1">Undertime</span></div>
                    <div class="legend_green"><i class="bi bi-square-fill"></i><span class="mx-1">Overtime</span></div>
                </div>
            </div>

            <div class="white_container row mt-3 p-4 mx-0 text-center justify-content-evenly align-items-center" id="table_container">
                <table class="table m-0 p-0" id="table_main"></table>
            </div>
        </div>
    </div>
</body>
</html>
