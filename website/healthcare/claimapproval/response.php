<?php

$clientno = $_POST["policyno"];
$treatmentno = $_POST["treatmentno"];
$result = $_POST["option"];

echo (shell_exec(" nodejs /home/whoami/hyperledger_serverside/fabcar/javascript/claimapprove-any.js $clientno $treatmentno $result"));

?>