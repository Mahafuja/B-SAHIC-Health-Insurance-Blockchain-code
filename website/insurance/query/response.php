<?php
echo "<style>
table, td, th {
    border: 1px solid black;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }
  </style>";
$clientno = $_POST["policyno"];
$myData = (shell_exec(" nodejs /home/whoami/hyperledger_serverside/fabcar/javascript/query.js $clientno"));
if ($myData[0] != "{") {
    die($myData);
}
// $uniquenumfromtime = microtime(true);
// $uniquenumfromtime*=pow(10, (strlen(substr(strrchr($uniquenumfromtime, "."), 1))));
$temparray = json_decode($myData, true);
echo "<br><br>";
$finalarray["name"] = $temparray["name"];
$finalarray["age"] = $temparray["age"];
$finalarray["medical_history"] = $temparray["medical_history"];
$finalarray["gender"] = $temparray["gender"];
$finalarray["lifetimesupport"] = $temparray["lifetimesupport"];

//if (empty($temparray["currentoperations"])) {
    //$temparray["currentoperations"] = "No current operations due";
//}
//if (empty($temparray["pendingclaims"])) {
    //$temparray["pendingclaims"] = "No current claims pending";
//}

//$finalarray["currentoperations"] = ($temparray["currentoperations"]);
//$finalarray["pendingclaims"] = $temparray["pendingclaims"];

$myData = json_encode($finalarray, true);
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

$finaloutput = jsonToTable(json_decode($myData));
if (strlen($finaloutput) > 100) {
    echo $finaloutput;
} else {
    echo "<h4>$myData<h4>";
}
