<?php

$ssn = $_POST["ssn"];
$name = $_POST["name"];
$age = $_POST["age"];
$gender = $_POST["gender"];
$contact = $_POST["mobileno"];
$medical_history = $_POST["medicalhistory"];
$occupation = $_POST["occupation"];
$nominees_details = $_POST["nominees_details"];
$issue_date = $_POST["issue_date"];
$maturity_date = $_POST["maturity_date"];
$plan_year = $_POST["plan_year"];
$address = $_POST["address"];
$microtime = floor(microtime(true));
$random = rand(1000, 9999);
$policynumber = "$microtime$random";
echo (shell_exec("nodejs /home/whoami/hyperledger_serverside/fabcar/javascript/createClient.js $policynumber $ssn $name $age $gender $contact $medical_history $occupation $nominees_details $issue_date $maturity_date $plan_year $address"));
// nodejs /home/whoami/hyperledger_serverside/fabcar/javascript/createClient.js ssn name age gender contact medical_history occupation nominees_details issue_date maturity_date plan_year address
