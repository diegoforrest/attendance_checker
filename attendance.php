<?php

// Database connection
$serverName = "LAPTOP-LSFR3CIB\SQLEXPRESS01";  // Change to your SQL Server name
$connectionOptions = [
    "Database" => "attendance",  // Change to your database name
    "Uid" => "",  // Use your SQL Server username
    "PWD" => ""   // Use your SQL Server password
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}
date_default_timezone_set('Asia/Manila'); 


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_attendance'])) {
    $student_id = $_POST['student_id'];
    $date = $_POST['date']; 
    $status = $_POST['status'];
    $time = date("H:i:s"); 

    $formatted_date = date("F j, Y", strtotime($date));  
    $formatted_time = date("H:i:s", strtotime($time));  

    $check_sql = "SELECT * FROM attendance WHERE student_id = ? AND date = ?";
    $check_params = [$student_id, $formatted_date];
    $check_stmt = sqlsrv_query($conn, $check_sql, $check_params);

    if (sqlsrv_has_rows($check_stmt)) {
        $message_date = "Attendance for this student on $formatted_date has already been marked!";
    } else {
        
        $sql = "INSERT INTO attendance (student_id, date, time, status) 
                VALUES (?, ?, ?, ?)";
        $params = [$student_id, $formatted_date, $formatted_time, $status];

        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $message = "Attendance marked successfully!";
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete_attendance_id'])) {
    $attendance_id = $_GET['delete_attendance_id'];

    $delete_sql = "DELETE FROM attendance WHERE attendance_id = ?";
    $delete_params = [$attendance_id];

    $delete_stmt = sqlsrv_query($conn, $delete_sql, $delete_params);

    if ($delete_stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $delete_message = "Attendance record deleted successfully!";
}

$sql = "SELECT * FROM interns";
$stmt = sqlsrv_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="image/favicon.png" type="image/png">
    <link rel="stylesheet" href="styles/attendance.css">
    <title>Mark Attendance</title>
</head>
<style>
   

    </style>
<body>

    <nav>
    <ul style="list-style-type: none; padding: 0;">
    <li style="display: flex; align-items: center; margin-top: 10px;">
    <a href="attendance.php" class="logo-link" style="display: flex; align-items: center; text-decoration: none;">
    <img src="image/icon-72.png" class="logo-img" style="max-height: 51px; margin-right: 10px;" />
    <span class="logo-text" style="font-family: 'Robotolightnew', sans-serif; line-height: 1.2; text-align: left; font-size: 20px;">
        Internship<br>Management
    </span>
</a>
</a>
    </li>
    <li style="margin-top: 25px; margin-left: 20px; font-family: 'Robotolightnew', sans-serif; font-size: 18px;">
        <a href="form.php" style="text-decoration: none;">Internship Registration Form</a>
    </li>
    <li style="margin-top: 25px; margin-left: 20px; font-family: 'Robotolightnew', sans-serif; font-size: 18px;">
        <a href="report.php" style="text-decoration: none;">Internship Report Time Out</a> <!-- Change report.php or report2.php -->
    </li>
        </ul>
    </nav>

    <h1 style="margin-top:40px;">Internship Attendance Time In</h1>
    <?php if (!empty($message_date)) { echo "<p style='color: red;'>$message_date</p>"; } ?>
    <?php if (!empty($message)) { echo "<p style='color: green;'>$message</p>"; } ?>
    <?php if (!empty($delete_message)) { echo "<p style='color: red;'>$delete_message</p>"; } ?>

<form method="POST">
    <label for="student_id">Select Intern:</label>
    <select name="student_id" id="student_id" required>
        <option value="" disabled selected>Select an Intern</option>
        <?php
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "<option value='" . $row['student_id'] . "'>" . $row['name'] . "</option>";
        }
        ?>
    </select>

    <label for="date">Date:</label>
    <input type="date" name="date" id="date" required>

    <label for="status">Status:</label>
    <select name="status" id="status" required>
        <option value="" disabled selected>Select Status</option>
        <option value="Present">Present</option>
        <option value="Absent">Absent</option>
    </select>

    <button class="btn2" type="submit" name="mark_attendance">Mark Attendance</button>
</form>
<div class="table-section">
    <h1>Interns Attendance Records</h1>
    <table>
        <tr>
            <th>Student ID</th>
            <th>Intern Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php

        $attendance_sql = "SELECT a.*, i.name FROM attendance a
                           JOIN interns i ON a.student_id = i.student_id
                           ORDER BY a.date DESC, a.time DESC";  
        $attendance_stmt = sqlsrv_query($conn, $attendance_sql);

        while ($row = sqlsrv_fetch_array($attendance_stmt, SQLSRV_FETCH_ASSOC)) {
            echo "<tr>
            <td>" . $row['student_id'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>" . $row['date']->format('F j, Y') . "</td>
            <td>" . $row['time']->format('h:i A') . "</td>
            <td>" . $row['status'] . "</td>
            <td>
                <a href='?delete_attendance_id=" . $row['attendance_id'] . "' 
                   class='btn' 
                   onclick='return confirm(\"Are you sure you want to delete this attendance record?\")'>
                    <i class='fa-regular fa-trash-can'></i> Delete
                </a>
            </td>
          </tr>";
        }    
        ?>
    </table>
    </div>
</body>
</html>
