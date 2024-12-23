<?php

$serverName = "LAPTOP-LSFR3CIB\SQLEXPRESS01";  // Change to your SQL Server name
$connectionOptions = [
    "Database" => "attendance",  // Change to your database name
    "Uid" => "",  
    "PWD" => ""   
];
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_intern'])) {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $school = $_POST['school'];
    $start_date = $_POST['start_date'];
    $department = $_POST['department'];
    $hours_required = $_POST['hours_required'];

    $check_sql = "SELECT * FROM interns WHERE student_id = ?";
    $check_params = array($student_id);
    $check_stmt = sqlsrv_query($conn, $check_sql, $check_params);

    if (sqlsrv_has_rows($check_stmt)) {
        $message = "Error: The student ID already exists. Please enter a unique student ID.";
    } else {
        $sql = "INSERT INTO interns (student_id, name, school, start_date, department, hours_required) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $params = [$student_id, $name, $school, $start_date, $department, $hours_required];

        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $message = "Intern added successfully!";
    }
}

if (isset($_GET['delete_id'])) {
    $student_id = $_GET['delete_id'];
    $sql = "DELETE FROM interns WHERE student_id = ?";
    $params = [$student_id];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $message = "Intern deleted successfully!";
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
    <link rel="stylesheet" href="styles/form.css">
    <link rel="icon" href="image/favicon.png" type="image/png">
    
    <title>Intern Form</title>
</head>

<body>

    <nav>
        <ul style="list-style-type: none; padding: 0;">
            <li style="display: flex; align-items: center; margin-top: 10px;">
                <a href="form.php" class="logo-link" style="display: flex; align-items: center; text-decoration: none;">
                    <img src="image/icon-72.png" class="logo-img" style="max-height: 51px; margin-right: 10px;" />
                    <span class="logo-text" style="font-family: 'Robotolightnew', sans-serif; line-height: 1.2; text-align: left; font-size: 20px;">
                        Internship<br>Management
                    </span>
                </a>
            </li>
            <li style="margin-top: 25px; margin-left: 20px; font-family: 'Robotolightnew', sans-serif; font-size: 18px;">
                <a href="attendance.php" style="text-decoration: none;">Internship Attendance</a>
            </li>
            <li style="margin-top: 25px; margin-left: 20px; font-family: 'Robotolightnew', sans-serif; font-size: 18px;">
                <a href="report.php" style="text-decoration: none;">Internship Report Time Out</a>  <!-- Change report.php or report2.php -->
            </li>
        </ul>
    </nav>

    <h1 style="margin-top:40px;">Internship Registration Form </h1>

    <?php if (!empty($message)) { echo "<p style='color: red;'>$message</p>"; } ?>
        <form method="POST">
            <label for="student_id">Student ID:</label>
            <input type="text" name="student_id" id="student_id" required value="">

            <label for="name">Intern Name:</label>
            <input type="text" name="name" id="name" required value="">

            <label for="school">School:</label>
            <input type="text" name="school" id="school" required value="">

            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" required value="">

            <label for="department">Department:</label>
            <input type="text" name="department" id="department" required value="">

            <label for="hours_required">Hours Required:</label>
            <input type="number" name="hours_required" id="hours_required" required value="">

            <button class="btn2"type="submit" name="submit_intern">Add Intern</button>
        </form>


    <div class="table-section">
    <h1> List of Registered Intern </h1>
        <table>
            <tr>
                <th>Student ID</th>
                <th>Intern Name</th>
                <th>School</th>
                <th>Start Date</th>
                <th>Department</th>
                <th>Hours Required</th>
                <th>Actions</th>
            </tr>
            <?php
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                echo "<tr>
                        <td>" . $row['student_id'] . "</td>
                        <td>" . $row['name'] . "</td>
                        <td>" . $row['school'] . "</td>
                        <td>" . $row['start_date']->format('F j, Y') . "</td>
                        <td>" . $row['department'] . "</td>
                        <td>" . $row['hours_required'] . "</td>
                        <td>
                            <a href='?delete_id=" . $row['student_id'] . "' class='btn' onclick='return confirm(\"Are you sure?\")'>
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

        