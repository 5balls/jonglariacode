<?php

include("../config/config.php");
include("../inc/database.inc.php");
include("../inc/getAge.inc.php");

$DB = new Database("mexicon");  

?>
<!DOCTYPE html 
     PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'
     'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='de' lang='de'>
<head>
<meta http-equiv='content-type' content='text/html; charset=iso-8859-1'/>
<title>Jonglaria</title>
<link rel='stylesheet' type='text/css' href='../registration.css' />
</head>
<body>
<div id='header'>
  <h1>6. Tübinger Jonglierconvention</h1>
  <h2>Teilnehmerliste [<a href='./participants.csv'>csv</a>]</h2>
</div>
<?php 

echo "<table class='participants'>\n";
echo "<tr>";
echo "<td><b>ID</b></td>";
echo "<td><b>Vorname</b></td>";
echo "<td><b>Nachname</b></td>";
echo "<td><b>Alter</b></td>";
echo "<td><b>&euro;</b></td>";
echo "<td><b>&#8224;</b></td>";
echo "<td><b>ZIP</b></td>";
echo "<td><b>IP</b></td>";
echo "</tr>\n";
$csv = fopen("participants.csv", "w");
fwrite($csv, "id, prename, surname, birthday, zip, email, payed, boat, regtime, arrivaltime, ip, browser, email-registered, email-ticket, active");
fwrite($csv, "\n");
$res = $DB->query("SELECT * FROM `participants`;");
while ($data = $DB->fetch_assoc($res)) {
  // table
  echo "<tr>";
  echo "<td>".$data['id']."</td>";
  echo "<td>".$data['prename']."</td>";
  echo "<td>".$data['surname']."</td>";
  echo "<td>".getAgeConvention($data['birthday'])."</td>";
  if ($data['payed']) echo "<td>&#x2714;</td>";
  else echo "<td>&#x2718;</td>";
  if ($data['boat']) echo "<td>&#x2714;</td>";
  else echo "<td>&#x2718;</td>";
  echo "<td>".$data['zip']."</td>";
  echo "<td>".$data['ip']."</td>";
  echo "</tr>\n";
  // csv file
  fwrite($csv, $data['id']);
  fwrite($csv, ",");
  fwrite($csv, $data['prename']);
  fwrite($csv, ",");
  fwrite($csv, $data['surname']);
  fwrite($csv, ",");
  fwrite($csv, $data['birthday']);
  fwrite($csv, ",");
  fwrite($csv, $data['zip']);
  fwrite($csv, ",");
  fwrite($csv, $data['email']);
  fwrite($csv, ",");
  fwrite($csv, $data['payed']);
  fwrite($csv, ",");
  fwrite($csv, $data['boat']);
  fwrite($csv, ",");
  fwrite($csv, $data['regtime']);
  fwrite($csv, ",");
  fwrite($csv, $data['arrivaltime']);
  fwrite($csv, ",");
  fwrite($csv, $data['ip']);
  fwrite($csv, ",");
  fwrite($csv, $data['browser']);
  fwrite($csv, ",");
  fwrite($csv, $data['email-registered']);
  fwrite($csv, ",");
  fwrite($csv, $data['email-ticket']);
  fwrite($csv, ",");
  fwrite($csv, $data['active']);
  fwrite($csv, "\n");
}
fclose($csv);
echo "</table>";

?>
