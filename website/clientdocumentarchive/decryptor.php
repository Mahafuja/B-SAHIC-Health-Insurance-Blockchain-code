<?php
define('FILE_ENCRYPTION_BLOCKS', 10000);
function decryptFile($source, $key, $dest)
{
    $key = substr(sha1($key, true), 0, 16);

    $error = false;
    if ($fpOut = fopen($dest, 'w')) {
        if ($fpIn = fopen($source, 'rb')) {
            // Get the initialzation vector from the beginning of the file
            $iv = fread($fpIn, 16);
            while (!feof($fpIn)) {
                $ciphertext = fread($fpIn, 16 * (FILE_ENCRYPTION_BLOCKS + 1)); // we have to read one block more for decrypting than for encrypting
                $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
                // Use the first 16 bytes of the ciphertext as the next initialization vector
                $iv = substr($ciphertext, 0, 16);
                fwrite($fpOut, $plaintext);
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
echo "Enter the path of the file to decrypt: ";
$fileName = trim(fgets(STDIN));
$fileName_original = $fileName;
// echo "\n";
echo "Enter the Policy number: ";
$clientno = trim(fgets(STDIN));
// echo "\n";
echo "Enter the Claim ID: ";
$treatmentid = trim(fgets(STDIN));
// echo "\n";
echo "Enter the Date and time: ";
$dateandtime = trim(fgets(STDIN));
$stringkey = $clientno . $treatmentid . $dateandtime;
// echo "\n";
$fileName = substr($fileName, 0, -8);
$filesnameparts = pathinfo($fileName);
$saveas = $filesnameparts['dirname'] . '/' . $filesnameparts['filename'] . "_decrypted." . $filesnameparts['extension'];
decryptFile($fileName_original, "$stringkey", $saveas);
