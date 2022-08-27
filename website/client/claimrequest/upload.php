<?php

/**
 * Define the number of blocks that should be read from the source file for each chunk.
 * For 'AES-128-CBC' each block consist of 16 bytes.
 * So if we read 10,000 blocks we load 160kb into memory. You may adjust this value
 * to read/write shorter or longer chunks.
 */
define('FILE_ENCRYPTION_BLOCKS', 10000);

/**
 * Encrypt the passed file and saves the result in a new file with ".enc" as suffix.
 *
 * @param string $source Path to file that should be encrypted
 * @param string $key    The key used for the encryption
 * @param string $dest   File name where the encryped file should be written to.
 * @return string|false  Returns the file name that has been created or FALSE if an error occured
 */
function encryptFile($source, $key, $dest)
{
    $key = substr(sha1($key, true), 0, 16);
    $iv = openssl_random_pseudo_bytes(16);

    $error = false;
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
            $claimid = "$treatmentno-$uniquenumfromtime";
            $output = (shell_exec("nodejs /home/whoami/hyperledger_serverside/fabcar/javascript/claimrequest-any.js $clientno $treatmentno $claimid"));
            echo $output;
            if ($output[0] == "F" || $output[20] == "b") // Failed
            {

                unlink($target_file);
                if ($folderwascreated == 1) {
                    rmdir($directory);
                }

            } else {

                date_default_timezone_set('Asia/Dhaka');
                $ts = date('Y-m-d H:i:s');

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
                $unn = $clientno . "-" . $claimid;
                $un = mysqli_real_escape_string(
                    $link, $unn);
                $umm = "http://localhost:83/$clientno/$file_name";
                $m = mysqli_real_escape_string(
                    $link, $umm);

                // Attempt insert query execution
                $sql = "INSERT INTO chats (ClientID, ClaimID, Attachments, Date_and_time)
		        VALUES ('$clientno', '$claimid', '$m', '$ts')";
                if (mysqli_query($link, $sql)) {

                } else {
                    echo "<br>ERROR: Message not sent!!!";
                    echo $un . "<br>" . $m . "<br>" . $ts . "<br>";
                }
                // Close connection
                mysqli_close($link);

            }

        } else {
            echo "failed to upload file";
        }

    } else {
        print_r($errors);
    }
}
