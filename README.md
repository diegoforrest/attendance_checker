# Attendance-checker-internship made by Diego Cruz
Attendance-checker-internship is made for single use of professor which it doesn't need a login/logout system.
 Attendanc-checker-internship allows the user to see the rendered time of the intern students

# Install requirements
Install Xampp V 8.0.30
https://www.apachefriends.org/download.html

Install DBMSS
https://learn.microsoft.com/en-us/sql/ssms/download-sql-server-management-studio-ssms?view=sql-server-ver16


Check PDF folder for installing
After installing all the software. Git clone https://github.com/diegoforrest/Attendance-checker-internship.git in xampp folder
xampp > htdocs 

# How to run #

After downloading xampp. Open the control panel
Start Apache
Open browser // http://localhost/attendance_checker/form.php

Open SSMS for the creating of database and tables. Connect to the server.
Dropdown the databases folder.
Right click on the databases folder > Add new database > name the database attendance
After creating the database. Refresh the SSMS to see the new database.
In the top of SSMS find the new query. click new query and the 

CREATE TABLE interns (
    student_id NVARCHAR(50) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    school NVARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    department NVARCHAR(50) NOT NULL,
    hours_required INT NOT NULL
);

CREATE TABLE attendance (
    id INT PRIMARY KEY IDENTITY(1,1),
    student_id NVARCHAR(50),  
    date DATE,                
    time TIME,                
    status NVARCHAR(20)       
);


ALTER TABLE attendance ADD paused_time DATETIME NULL; --execture after the table creation of attendance
ALTER TABLE attendance ADD timeout_time DATETIME NULL; --execture after the table creation of attendance

// I created two report (report.php and report2.php) choose what you want to use. 
// After choosing what to use. edit the code. look the comment in each php file

## Authors
[Diego Cruz](https://github.com/diegoforrest)






