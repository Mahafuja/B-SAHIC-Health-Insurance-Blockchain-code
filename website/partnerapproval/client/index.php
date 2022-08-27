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
function flipDiagonally($arr)
{
   $out = array();
   foreach ($arr as $key => $subarr) {
      foreach ($subarr as $subkey => $subvalue) {
         $out[$subkey][$key] = $subvalue;
      }
   }
   return $out;
}
function jsonToTable($data)
{
   $table = '
    <table class="json-table" width="100%">
    ';
   foreach ($data as $key => $value) {
      $table .= '
        <tr valign="top">
        ';
      if (!is_numeric($key)) {
         $table .= '
            <td>
                <strong>' . $key . ':</strong>
            </td>
            <td>
            ';
      } else {
         $table .= '
            <td colspan="2">
            ';
      }
      if (is_object($value) || is_array($value)) {
         $table .= jsonToTable($value);
      } else {
         $table .= $value;
      }
      $table .= '
            </td>
        </tr>
        ';
   }
   $table .= '
    </table>
    ';
   return $table;
}

function build_table($array)
{
   // start table
   $html = '<table>';
   // // header row
   $html .= "<tr><th>Client ID</th><th>Address</th><th>Age</th><th>Contact No.</th><th>Current Operations</th><th>Gender</th><th>Registration Date</th><th>Lifetime Support</th><th>Maturity Date</th><th>Medical History</th><th>Name</th><th>Nominee Details</th><th>Occupation</th><th>Pending Claims</th><th>Plan Year</th><th>Social Security No.</th><th>Yearly Quota used (times)</th><th>Yearly Quota used (money)</th></tr>";
   // $html .= '<tr>';
   // foreach($array[0] as $key=>$value){
   //         $html .= '<th>' . htmlspecialchars($key) . '</th>';
   //     }
   // $html .= '</tr>';

   // data rows
   foreach ($array as $key => $value) {
      $html .= '<tr>';
      foreach ($value as $key2 => $value2) {
         if ($value2 == []) {
            $value2 = null;
         }
         if ($key2 == 'docType') {
            continue;
         } else if ($key2 == 'currentoperations') {
            $value2 = (json_encode($value2));
         } else if ($key2 == 'pendingclaims') {
            $value2 = (json_encode($value2));
         }
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
   $togglingclient = $_POST['Name'];
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

   if ($numofrows < 1) { //hospitalname not currently in use, so create client account
      $sql = "INSERT INTO hospitals (Name, RegistrationDate, Address, companycell, password, ContractFreeze) VALUES ('$thiscompanyname-$hospitalname', '$registrationdate', '$address', '$companycell', '$password', 0)";
      if ($conn->query($sql) === true) {
         $registerbuttonvalue = "Registration Completed!"; //These values will be output down there
      } else {
         $registerbuttonvalue = "Client Registration Failed!";
      }
   } else {
      $registerbuttonvalue = "Client Name Already in use!";
   }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Client Status Panel</title>
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


      th,
      td {
         border: 1px solid black;
         padding: 10px
      }

      table,
      th,
      td {
         text-align: center;
         /* border: 1px solid black; */
         /* padding: 10px; */
         border-spacing: 0px;
      }

      table {
         padding: 0px;
         max-width: 100%;
         white-space: nowrap;
         table-layout: fixed border:0;
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
   <h1>Monitor/Manage Client centers' claims accepted by partnered insurance companies</h1>

   <h2>Client Status Panel - Showing all hospitals partnered by insurance companies</h2>
   <div class="center">
      <?php

      $resultarr = json_decode(shell_exec("nodejs /home/whoami/hyperledger_serverside/fabcar/javascript/queryall.js"));
      $resultarr = json_decode(json_encode($resultarr), true);
      $newarr = [];
      foreach ($resultarr as $subarray) {
         $clientID = array(ID => $subarray[Key]);
         $clientRecord = $subarray[Record];
         $clientdata = array_merge($clientID, $clientRecord);
         array_push($newarr, $clientdata);
      }
      // print_r($newarr);
      echo build_table($newarr);
      ?>
   </div>
</body>

</html>