<?php

if (isset($_POST['submit'])){
	/* Attempt MySQL server connection. Assuming
you are running MySQL server with default
setting (user 'root' with no password) */
	$link = mysqli_connect("localhost",
				"root", "12345678", "chat_app");

	// Check connection
	if($link === false){
		echo ("Connection failed");
		die("ERROR: Could not connect. "
			. mysqli_connect_error());
	}
	else{
		echo "Connected!";
	}

	// Escape user inputs for security
	$un= mysqli_real_escape_string(
		$link, $_REQUEST['ClientID']);
	$m = mysqli_real_escape_string(
		$link, $_REQUEST['Attachments']);
	date_default_timezone_set('Asia/Dhaka');
	$ts=date('Y-m-d H:i:s');
	// Attempt insert query execution
	$sql = "INSERT INTO chats (ClientID, TreatmentID, Attachments, Date_and_time)
			VALUES ('$clientno', '$treatmentno', '$m', '$ts')"; 
	if(mysqli_query($link, $sql)){
		
	} else{
		echo "ERROR: Message not sent!!!";
		echo $un . "<br>" .$m . "<br>".$ts . "<br>"; 
	}
	// Close connection
	mysqli_close($link);
	}
?>
<html>
<head>
	<meta http-equiv="refresh" content="5">
<style>
.vertical-menu {
  width: 400px;
}

*{
	box-sizing:border-box;
}
body{
	background-color:#abd9e9;
	font-family:Arial;
}
.col-25 {
        float: left;
	    display:inline-block;
	    font-size:15px;
        width: 25%;
        margin-top: 6px;
      }
#container{
	width:500px;
	height:700px;
	background:white;
	margin:0 auto;
	font-size:0;
	border-radius:5px;
	overflow:hidden;
}
main{
	width:500px;
	height:700px;
	display:inline-block;
	font-size:15px;
	vertical-align:top;
}
main header{
	height:100px;
	padding:30px 20px 30px 40px;
	background-color:#622569;
}
main header > *{
	display:inline-block;
	vertical-align:top;
}
main header img:first-child{
	width:24px;
	margin-top:8px;
}
main header img:last-child{
	width:24px;
	margin-top:8px;
}
main header div{
	margin-left:90px;
	margin-right:90px;
}
main header h2{
	font-size:25px;
	margin-top:5px;
	text-align:center;
	color:#FFFFFF;
}
main .inner_div{
	padding-left:0;
	margin:0;
	list-style-type:none;
	position:relative;
	overflow:auto;
	height:500px;
	background-image:url(
https://media.geeksforgeeks.org/wp-content/cdn-uploads/20200911064223/bg.jpg);
	background-position:center;
	background-repeat:no-repeat;
	background-size:cover;
	position: relative;
	border-top:2px solid #fff;
	border-bottom:2px solid #fff;
	
}
main .triangle{
	width: 0;
	height: 0;
	border-style: solid;
	border-width: 0 8px 8px 8px;
	border-color: transparent transparent
	#58b666 transparent;
	margin-left:20px;
	clear:both;
}
main .message{
	padding:10px;
	color:#000;
	margin-left:15px;
	background-color:#58b666;
	line-height:20px;
	max-width:90%;
	display:inline-block;
	text-align:left;
	border-radius:5px;
	clear:both;
}
main .triangle1{
	width: 0;
	height: 0;
	border-style: solid;
	border-width: 0 8px 8px 8px;
	border-color: transparent
	transparent #6fbced transparent;
	margin-right:20px;
	float:right;
	clear:both;
}
main .message1{
	padding:10px;
	color:#000;
	margin-right:15px;
	background-color:#6fbced;
	line-height:20px;
	max-width:90%;
	display:inline-block;
	text-align:left;
	border-radius:5px;
	float:right;
	clear:both;
}

main footer{
	height:150px;
	padding:20px 30px 10px 20px;
	background-color:#622569;
}
main footer .input1{
	resize:none;
	border:100%;
	display:block;
	width:120%;
	height:55px;
	border-radius:3px;
	padding:20px;
	font-size:13px;
	margin-bottom:13px;
}
main footer textarea{
	resize:none;
	border:100%;
	display:block;
	width:140%;
	height:55px;
	border-radius:3px;
	padding:20px;
	font-size:13px;
	margin-bottom:13px;
	margin-left:20px;
}
main footer .input2{
	resize:none;
	border:100%;
	display:block;
	width:40%;
	height:55px;
	border-radius:3px;
	padding:20px;
	font-size:13px;
	margin-bottom:13px;
	margin-left:100px;
	color:white;
	text-align:center;
	background-color:black;
	border: 2px solid white;
}
}
main footer textarea::placeholder{
	color:#ddd;
}

</style>
<body onload="show_func()">
<div class="vertical-menu">
<div id="container">
	<main>
		<header>
			<img src="https://s3-us-west-2.amazonaws.com/
			s.cdpn.io/1940306/ico_star.png" alt="">
			<div>
				<h2>Group Chat</h2> 
			</div>
			<img src="https://s3-us-west-2.amazonaws.com/
			s.cdpn.io/1940306/ico_star.png" alt="">
		</header>

<script>
function show_func(){

var element = document.getElementById("chathist");
	element.scrollTop = element.scrollHeight;

}
</script>

<form id="myform" action="Group_chat.php" method="POST" >
<div class="inner_div" id="chathist">
<?php
$host = "localhost";
$user = "root";
$pass = "12345678";
$db_name = "chat_app";
$con = new mysqli($host, $user, $pass, $db_name);

$query = "SELECT * FROM chats";
$run = $con->query($query);
$i=0;

while($row = $run->fetch_array()) :
if($i==0){
$i=5;
$first=$row;
?>
<div id="triangle1" class="triangle1"></div>
<div id="message1" class="message1">
<span style="color:white;float:right;">
<a href="<?php echo $row['Attachments']; ?>" target="_blank">Attachments from Patients</a>
</span> <br/> 
<div>
<span style="color:black;float:left;
font-size:10px;clear:both;">
	<?php echo $row['ClientID']; ?>,
		<?php echo $row['Date_and_time']; ?> 
</span>
</div>
</div>
<br/><br/>
<?php
}
else
{
if($row['ClientID']!=$first['ClientID'])
{
?>
<div id="triangle" class="triangle"></div>
<div id="message" class="message">
<span style="color:white;float:left;">
<a href="<?php echo $row['Attachments']; ?>" target="_blank">Attachments from Healthcare</a>
</span> <br/>
<div>
<span style="color:black;float:right;
		font-size:10px;clear:both;">
<?php echo $row['ClientID']; ?>,
		<?php echo $row['Date_and_time']; ?>
		
</span>
</div>
</div>
<br/><br/>
<?php
}
else
{
?>
<div id="triangle1" class="triangle1"></div>
<div id="message1" class="message1">
<span style="color:white;float:right;">
<?php echo $row['Attachments']; ?>
</span> <br/>
<div>
<span style="color:black;float:left;
		font-size:10px;clear:both;">
<?php echo $row['ClientID']; ?>,
	<?php echo $row['Date_and_time']; ?>
</span>
</div>
</div>
<br/><br/>
<?php
}
}
endwhile;
?>
</div>
</form>
</main>	
</div>
</div>
</body>
</html>
