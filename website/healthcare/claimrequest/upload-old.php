
<?php
//var_dump(function_exists('mysqli_connect'));
if (isset($_FILES['imageToUpload'])) {
    $errors = array();
    $directory = "/var/www/html/uploads/";
    $file_name = basename($_FILES['imageToUpload']['name']);

    $file_size = $_FILES['imageToUpload']['size'];
    $file_tmp = $_FILES['imageToUpload']['tmp_name'];
    $file_type = $_FILES['imageToUpload']['type'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    // echo move_uploaded_file($_FILES["imageToUpload"]["tmp_name"],$target_file);
    $expensions = array("jpeg", "jpg", "png", "pdf", "webp");

    if (in_array($file_ext, $expensions) === false) {
        $errors[] = "extension not allowed, please choose a JPEG, PNG or PDF file.";
    }

    if ($file_size > 2097152) {
        $errors[] = 'File size must be excately 2 MB';
    }
    if (file_exists($target_file)) {
        $errors[] = 'File already Exist ' . $target_file;
    }
    $clientno = $_POST["policyno"];
    $treatmentno = $_POST["treatmentno"];
    echo "$directory$clientno";
    echo "<br>";
    if (empty($errors) == true) {
        if (file_exists($directory . $clientno) == false) {

            mkdir("$directory$clientno", 0777, true);
            $folderwascreated = 1;
        } else {
            $folderwascreated = 0;
        }
        $directory = $directory . $clientno;
        $target_file = $directory . "/" . $file_name;
        $uploadOk = move_uploaded_file($_FILES["imageToUpload"]["tmp_name"], $target_file);
        if ($uploadOk) {
            $uniquenumfromtime = microtime(true);
            $uniquenumfromtime *= pow(10, (strlen(substr(strrchr($uniquenumfromtime, "."), 1))));
            $output = (shell_exec("nodejs /home/whoami/hyperledger_serverside/fabcar/javascript/claimrequest-any.js $clientno $treatmentno '$treatmentno-$uniquenumfromtime'"));
            echo $output;
            if ($output[0] == "F" || $output[20] == "b") // Failed
            {
                unlink($target_file);
                if ($folderwascreated == 1) {
                    rmdir($directory);
                }
            } else {
                echo "<br>Successfully uploaded " . htmlspecialchars(basename($_FILES["imageToUpload"]["name"]));

                $link = mysqli_connect("localhost",
                    "root", "12345678", "chat_app");

                // Check connection
                if ($link === false) {
                    echo ("Connection failed");
                    die("ERROR: Could not connect. "
                        . mysqli_connect_error());
                }

                // Escape user inputs for security
                $unn = $clientno . "-" . $treatmentno;
                $un = mysqli_real_escape_string(
                    $link, $unn);
                $umm = "http://localhost:83/$clientno/" . $file_name;
                $m = mysqli_real_escape_string(
                    $link, $umm);
                date_default_timezone_set('Asia/Dhaka');
                $ts = date('Y-m-d H:i:s');
                // Attempt insert query execution
                $sql = "INSERT INTO chats (ClientID, TreatmentID, Attachments, Date_and_time)
		        VALUES ('$clientno', '$treatmentno', '$m', '$ts')";
                if (mysqli_query($link, $sql)) {

                } else {
                    echo "ERROR: Message not sent!!!";
                    echo $un . "<br>" . $m . "<br>" . $ts . "<br>";
                }
                // Close connection
                mysqli_close($link);

            }

        } else {
            echo "failed to upload the file";
        }

    } else {
        print_r($errors);
    }
}

?>


