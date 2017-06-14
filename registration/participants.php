<?php

include("config/config.php");
include("inc/database.inc.php");

$DB = new Database("mexicon");  

?>
<!DOCTYPE html 
     PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'
     'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='de' lang='de'>
<head>
<meta http-equiv='content-type' content='text/html; charset=iso-8859-1'/>
<title>Jonglaria</title>
<link rel='stylesheet' type='text/css' href='./registration.css' />
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
echo "<td><b>Gezahlt</b></td>";
echo "<td><b>IP</b></td>";
echo "</tr>\n";
$csv = fopen("participants.csv", "w");
$res = $DB->query("SELECT * FROM `participants`;");
while ($data = $DB->fetch_assoc($res)) {
  // table
  echo "<tr>";
  echo "<td>".$data['id']."</td>";
  echo "<td>".$data['prename']."</td>";
  echo "<td>".$data['surname']."</td>";
  echo "<td>".$data['payed']."</td>";
  echo "<td>".$data['ip']."</td>";
  echo "</tr>\n";
  // csv file
  fwrite($csv, $data['id']);
  fwrite($csv, ",");
  fwrite($csv, $data['prename']);
  fwrite($csv, ",");
  fwrite($csv, $data['surname']);
  fwrite($csv, "\n");
}
fclose($csv);
echo "</table>";

?>
