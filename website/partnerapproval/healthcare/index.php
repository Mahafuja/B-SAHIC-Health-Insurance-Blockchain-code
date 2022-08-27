<?php
//Connect to MySQL database server
$host = "localhost";
$user = "root";
$pass = "12345678";
$db_name = "chat_app";
$conn = new mysqli($host, $user, $pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: Database Server is down/unavailable");
}
date_default_timezone_set('Asia/Dhaka');

function build_table($array)
{
    // start table
    $html = '<table>';
    // // header row
    $html .= "<tr><th>Registration ID</th><th>Hospital Legal Name</th><th>Date of Registration</th><th>Last login time</th><th>Address</th><th>Company Phone Number</th><th>Hospital Contract Terminated</th></tr>";
    // $html .= '<tr>';
    // foreach($array[0] as $key=>$value){
    //         $html .= '<th>' . htmlspecialchars($key) . '</th>';
    //     }
    // $html .= '</tr>';

    // data rows
    foreach ($array as $key => $value) {
        $html .= '<tr>';
        foreach ($value as $key2 => $value2) {
            if ($key2 == 'ContractFreeze') {
                if ($value[ContractFreeze] == 0) {
                    $terminationtext = 'No';
                    $terminationtoggle = 1;
                    $terminationcolor = 'background-color:lightgreen';
                } else if ($value[ContractFreeze] == 1) {
                    $terminationtext = 'Yes';
                    $terminationtoggle = 0;
                    $terminationcolor = 'background-color:#ffcccb';
                }

                $value2 = "
                <form action='./' method='post' enctype='multipart/form-data'>
                  <input type='text' name='Name' value='$value[Name]' hidden>
                  <input type='text' name='terminationtoggle' value='$terminationtoggle' hidden>
                  <input type='submit' id='termisubmit' value='$terminationtext' style='$terminationcolor' />
</form>
";
            }
            $html .= '<td>' . ($value2) . '</td>';

        }
        $html .= '</tr>';
    }

// finish table and return it

    $html .= '</table>';
    return $html;
}

$registerbuttonvalue = "Register Hospital"; //Back to default button value

if (isset($_COOKIE['name'])) {
    $thiscompanyname = $_COOKIE['name'];
}

if (isset($_POST['terminationtoggle'])) {
    $togglinghospital = $_POST['Name'];
    $terminationtoggle = $_POST['terminationtoggle'];
    $sql = "UPDATE hospitals SET ContractFreeze = '$terminationtoggle' WHERE Name = '$togglinghospital';";
    $conn->query($sql);
} else if (isset($_POST['hospitalname'])) {
    $hospitalname = $_POST['hospitalname'];
    $registrationdate = date('Y-m-d H:i:s');
    $address = $_POST['address'];
    $companycell = $_POST['companycell'];
    $password = $_POST['password'];

//First check to see if this hospitalname is already used by another insurance company
    $query = "SELECT * FROM hospitals WHERE Name = '$hospitalname'";
    $result = mysqli_query($conn, $query);
    $numofrows = mysqli_num_rows($result);

    if ($numofrows < 1) { //hospitalname not currently in use, so create hospital account
        $sql = "INSERT INTO hospitals (Name, RegistrationDate, Address, companycell, password, ContractFreeze) VALUES ('$thiscompanyname-$hospitalname', '$registrationdate', '$address', '$companycell', '$password', 0)";
        if ($conn->query($sql) === true) {
            $registerbuttonvalue = "Registration Completed!"; //These values will be output down there
        } else {
            $registerbuttonvalue = "Hospital Registration Failed!";
        }
    } else {
        $registerbuttonvalue = "Hospital Name Already in use!";
    }

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Insurance Hospital Status Panel</title>
   <style>
   .center {

      text-align: center;
      display: block;
      margin-left: auto;
      margin-right: auto;

   }

   * {
      box-sizing: border-box;
   }

   body {
      background-color: #abd9e9;
      font-family: Arial;
      text-align: left;
   }

   tr td:last-child {
      white-space: nowrap;
   }

   td {
      padding: 0px;
      text-align: right;
      border: none;
   }

   form {
      margin: 0;
      padding: 0;
   }

   table {
      padding: 10px;
      max-width: 100%;
      white-space: nowrap;
      table-layout: fixed border:0;
   }

   th,
   td {
      border: 1px solid black;
   }

   table,
   th,
   td {
      text-align: center;
      /* border: 1px solid black; */
      padding: 10px;
      border-spacing: 0px;
   }

   .paddingBetweenCols td {
      padding: 0 15px;
   }

   .vertical-menu {
      width: 1200px;
   }

   * {
      box-sizing: border-box;
   }

   h1 {
      text-align: center;
      font-size: 30px;
   }

   input[type=text],
   select,
   textarea {
      width: 50%;
      padding: 12px;
      border: 1px solid rgb(43, 226, 141);
      border-radius: 4px;
      resize: vertical;
      font-size: 20px;
   }

   label {
      padding: 12px 12px 12px 0;
      display: inline-block;
   }

   input[type=submit] {
      background-color: rgb(98, 255, 255);
      color: black;
      padding: 15px 25px;
      border: none;
      position: relative;
      border-radius: 4px;
      cursor: pointer;
      float: right;
      font-size: 20px;
   }

   input[type=button] {
      color: black;
      padding: 15px 25px;
      border: none;
      position: relative;
      border-radius: 4px;
      cursor: pointer;
      float: right;
      font-size: 20px;
      background-color: orange;
   }

   #submit:hover {
      background-color: #62b0ff;
   }

   #termisubmit {
      border-radius: 25px;
      text-align: center;
      margin-right: 5%;
      width: 90%;
      height: 80%;
   }

   #termisubmit:hover {
      background-color: #62b0ff;
   }

   input[type=button]:hover {
      background-color: #62b0ff;
   }

   .container {
      border-radius: 5px;
      background-color: #f2f2f2;
      padding: 20px;
      width: 700px;
   }

   .col-25 {
      float: left;
      width: 25%;
      margin-top: 5px;
      font-size: 20px;
   }

   .col-75 {
      float: left;
      width: 55%;
      margin-top: 5px;
   }

   /* Clear floats after the columns */
   .row:after {
      content: "";
      display: table;
      clear: both;
   }

   /* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
   @media screen and (max-width: 500px) {

      .col-25,
      .col-75,
      input[type=submit] {
         width: 80%;
         margin-top: 0;
      }
   }
   </style>

</head>

<body>
   <h1>Monitor/Manage Healthcare centers' claims accepted by partnered insurance companies</h1>

   <h2>Hospital Status Panel - Showing all hospitals partnered by insurance companies</h2>
   <div class="center">
      <?php

$query = "SELECT ID, Name, RegistrationDate, LastLogin, Address, companycell, ContractFreeze FROM  hospitals"; //You don't need a ; like you do in SQL

$result = mysqli_query($conn, $query);
$resultarr = (mysqli_fetch_all($result, MYSQLI_ASSOC));

//   print_r($resultarr);
echo build_table($resultarr);

?>
   </div>
</body>

</html>