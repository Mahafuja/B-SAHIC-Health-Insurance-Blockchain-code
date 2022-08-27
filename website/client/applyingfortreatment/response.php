<?php

$clientno = $_POST["policyno"];
$treatmentno = $_POST["treatmentno"];

echo (shell_exec("nodejs /home/whoami/hyperledger_serverside/fabcar/javascript/cashless.js $clientno $treatmentno"));

?>
