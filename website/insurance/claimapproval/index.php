<?php

function createDir($path, $mode = 0777, $recursive = true)
{
    if (file_exists($path)) {
        return true;
    }

    return mkdir($path, $mode, $recursive);
}

define('FILE_ENCRYPTION_BLOCKS', 10000);
/**
 * Encrypt the passed file and saves the result in a new file with ".enc" as suffix.
 *
 * @param string $source Path to file that should be encrypted
 * @param string $key    The key used for the encryption
 * @param string $dest   File name where the encryped file should be written to.
 * @return string|false  Returns the file name that has been created or FALSE if an error occured
 */
function encryptFile($source, $key, $dest, $folder)
{
    $key = substr(sha1($key, true), 0, 16);
    $iv = openssl_random_pseudo_bytes(16);
    createDir($folder, 0777, true);
    if ($fpOut = fopen($dest, 'w')) {
        // Put the initialzation vector to the beginning of the file
        fwrite($fpOut, $iv);
        if ($fpIn = fopen($source, 'rb')) {
            while (!feof($fpIn)) {
                $plaintext = fread($fpIn, 16 * FILE_ENCRYPTION_BLOCKS);
                $ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                // Use the first 16 bytes of the ciphertext as the next initialization vector
                $iv = substr($ciphertext, 0, 16);
                fwrite($fpOut, $ciphertext);
            }
            fclose($fpIn);
        } else {
            $error = true;
        }
        fclose($fpOut);
    } else {
        $error = true;
    }

    return $error ? false : $dest;
}

function jsonToTable($data)
{
    $table = '
    <table class="json-table" cellpadding="0" cellspacing="15" border=1 style="max-width:100%">
    ';
    foreach ($data as $key => $value) {
        $table .= '
        <tr valign="top">
        ';
        if (!is_numeric($key)) {
            if ($key == "ClientID") $key = "PolicyNo";
            $table .= '
            <td align="right" valign="middle">
                <strong>' . $key . ':</strong>
            </td>

            <td style="text-align:left">';
        } else {
            $table .= '
            <td colspan="1" align="right" valign="middle">
            ';
        }
        if (is_object($value) || is_array($value)) {
            $table .= jsonToTable($value);
        } else {
            if ($value[0] . $value[1] == "ht") {
                $table .= '<a target="_blank" href="' . $value . '">Attachment of patients documents. </a>';
            } else {
                $table .= $value;
            }
        }
        $table .= '
            </td>
        </tr>
        ';
    }
    $table .= '
	<tr  valign="top">
		<td align="right" valign="middle" width=200>
			<strong>Acceptance:</strong>
		</td>
		<td style="text-align:left">
		<form id="myform" action="index.php" style="padding:0;" method="POST">';
    $clientnum = $data[ClientID];
    $treatmentnum = $data[ClaimID];
    $attachmentid = $data[Attachments];
    $dateandtime = $data[Date_and_time];
    $table .= "
		<input type=\"hidden\" id=\"policyno\" name=\"policyno\" value=\"$clientnum\">
		<input type=\"hidden\" id=\"claimid\" name=\"claimid\" value=\"$treatmentnum\">
        <input type=\"hidden\" id=\"attachmentid\" name=\"attachmentid\" value=\"$attachmentid\">
        <input type=\"hidden\" id=\"dateandtime\" name=\"dateandtime\" value=\"$dateandtime\">
		";
    $table .= '
			<label for="Accept">Accept</label>
			<input type="radio" name="option" id="option" value="1" onchange="this.form.submit()"/>
			<label for="Decline">Decline</label>
			<input type="radio" name="option" id="option" value="0" onchange="this.form.submit()"/>
		</form>
		</td>
	</tr>
    </table>
    ';
    return $table;
}

if (isset($_POST['policyno'])) {
    $clientno = $_POST["policyno"];
    $claimid = $_POST["claimid"];
    $result = $_POST["option"];
    $dateandtime = $_POST["dateandtime"];
    $target_file = $_POST["attachmentid"];

    $target_file = substr($target_file, 20); //target_file is now ClientID/AttachmentID
    shell_exec("nodejs /home/whoami/hyperledger_serverside/fabcar/javascript/claimapprove-any.js $clientno $claimid $result");
    $link = mysqli_connect("localhost",
        "root", "12345678", "chat_app");
    // Check connection
    if ($link === false) {
        echo ("Connection failed");
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    // Escape user inputs for security
    $un = mysqli_real_escape_string(
        $link, $_REQUEST['ClientID']);
    $m = mysqli_real_escape_string(
        $link, $_REQUEST['Attachments']);
    date_default_timezone_set('Asia/Dhaka');
    $ts = date('Y-m-d H:i:s');
    // Attempt insert query execution
    $sql = "DELETE FROM chats WHERE ClientID='$clientno' AND ClaimID='$claimid';";
    if (mysqli_query($link, $sql)) {
    } else {
        echo "<br>ERROR: Message not sent!!!";
        echo $un . "<br>" . $m . "<br>" . $ts . "<br>";
    }
    // Close connection
    mysqli_close($link);

    //Secret key is ClientID+TreatmentID+Time_of_Acceptance_of_Claim_Request
    //$secretkey = "$clientno . $claimid . $ts";
    $secretkey = $clientno . $claimid . $dateandtime;
    encryptFile("/var/www/html/uploads/$target_file", $secretkey, "/var/www/html/clientdocumentarchive/$target_file" . '.encrypt', "/var/www/html/clientdocumentarchive/" . substr($target_file, 0, 14));
    unlink("/var/www/html/uploads/$target_file");

    header('Location: /claimapproval/');
}
?>
<html>
<head>
	<!--meta http-equiv="refresh" content="5"-->
	<meta charset="UTF-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<title>Claim Approval Panel</title>
<style>
*{
	box-sizing:border-box;
}
body{
	background-color:#abd9e9;
	font-family:Arial;
	text-align:center;
}
tr td:last-child {
    white-space: nowrap;
}
td
{
    padding:0px;
	text-align:right;
	border:none;
}
form
{
	margin:0;
	padding:0;
}
table
{
	padding:10px;
	max-width:100%;
	white-space:nowrap;
	text-align:right;
	table-layout: fixed
}
.paddingBetweenCols td
{
    padding:0 15px;
}
</style>
<body onload="show_func()">
	<h1 style="display:block;">Claim Approval Panel</h1>
	<main style="left:30%; position:absolute;">
<?php
$host = "localhost";
$user = "root";
$pass = "12345678";
$db_name = "chat_app";
$con = new mysqli($host, $user, $pass, $db_name);
$query = "SELECT * FROM chats";
$run = $con->query($query);
$i = 0;

//create an array
$emparray = array();
while ($roww = mysqli_fetch_assoc($run)) {
    $emparray[] = $roww;
}
$table = jsonToTable($emparray);
$table = substr($table, 0, strrpos($table, "<tr  valign=\"top\">"));
$table .= '</tbody></table>';
echo $table;
?>
</main>
</body>
</html>
