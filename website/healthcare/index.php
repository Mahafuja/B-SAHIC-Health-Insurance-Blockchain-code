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

if (isset($_POST['Name'])) {
    // print_r($_POST);
    $name = $_POST['NameInsurance'] . '-' . $_POST['Name'];
    $pasword = $_POST['password'];
    $currentime = date('Y-m-d H:i:s');
    //Check if exists
    $query = "SELECT * FROM hospitals WHERE Name = '$name' AND password = '$pasword'";
    $result = mysqli_query($conn, $query);
    $numofrows = mysqli_num_rows($result);
    if ($numofrows != 0) {
        //Correct username and password
        //Check if account is enabled
        $query = "SELECT * FROM hospitals WHERE Name = '$name' AND password = '$pasword' AND ContractFreeze = '0'";
        $result = mysqli_query($conn, $query);
        $numofrows = mysqli_num_rows($result);
        if ($numofrows != 0) {
            setcookie('name', $name, time() + (86400 * 30), "/"); // 86400 = 1 day
            echo "login successful";
            $query = "UPDATE hospitals SET LastLogin='$currentime' WHERE Name = '$name' AND password = '$pasword'";
            $result = mysqli_query($conn, $query);
            header("Location: /loggedin.html");
        } else {
            echo "<text style=\"color:red;font-weight:bold\"> Login failed: Account is disabled by Adminstrator!</text>";
        }
    } else {
        //Login unsuccessful
        echo "<text style=\"color:red;font-weight:bold\"> Login failed: wrong hospital name OR password combination <br><br> Try again!</text>";
    }
}

?>
<!DOCTYPE html>
<html>

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Healthcare login Panel</title>
   <style>
   .vertical-menu {
      width: 500px;
   }

   body {
      font-family: Arial, Helvetica, sans-serif;
   }

   form {
      border: 3px solid #f1f1f1;
   }

   h2 {
      text-align: center;
      font-size: 30px;
   }

   h1 {
      display: inline;
      font-size: 20px;
   }

   input[type=text],
   input[type=password] {
      width: 100%;
      padding: 12px 20px;
      margin: 8px 0;
      display: inline-block;
      border: 1px solid #ccc;
      box-sizing: border-box;
   }

   button {
      background-color: #04AA6D;
      color: white;
      padding: 14px 20px;
      margin: 8px 0;
      border: none;
      cursor: pointer;
      width: 100%;
      font-size: 13pt;
   }

   button:hover {
      opacity: 0.8;
   }

   .cancelbtn {
      width: auto;
      padding: 10px 18px;
      background-color: #f44336;
   }

   .imgcontainer {
      text-align: center;
      margin: 4px 0 2px 0;
   }

   img.avatar {
      /* width: 15%; */
      /* border-radius: 4%; */
   }

   .container {
      padding: 16px;
   }

   span.password {
      float: right;
      padding-top: 16px;
   }

   /* Change styles for span and cancel button on extra small screens */
   @media screen and (max-width: 300px) {
      span.password {
         display: block;
         float: none;
      }

      .cancelbtn {
         width: 100%;
      }
   }
   </style>
</head>

<body style="background-color:powderblue;">

   <body>
      <div class="vertical-menu">

         <h2>Hospital Login Form</h2>

         <form action="./" method="post" enctype="multipart/form-data">
            <div class="imgcontainer">
               <img src="healthcare.jpeg" alt="Avatar" class="avatar">
            </div>
            <div class="container">
               <h1><label for="Name"><b>Hospital Name</b></label>
               <input type="text" placeholder="Enter Hospital name" name="Name" required></h1>

               <h1><label for="NameInsurance"><b>Insurance Company Partnered with</b></label>
               <input type="text" placeholder="Enter Insurance Company name" name="NameInsurance" required></h1>

               <h1><label for="password"><b>Access Key</b></label>
               <input type="password" placeholder="Enter Access Key" name="password" required></h1>

               <h1> <button type="submit" style="font-size:13pt">Login</button></h1>
               <label>
                  <input type="checkbox" checked="checked" name="remember"> Remember me
               </label>
            </div>
      </div>
      </form>
   </body>

</html>
