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
<meta http-equiv='content-type' content='text/html; charset=<?php echo CHARSET; ?>'/>
<title>Jonglaria</title>
<link rel='stylesheet' type='text/css' href='../style.css' />
</head>
<body>
<div id='header'>
  <h1>Galashow der 6. Tübinger Jonglierconvention</h1>
  <h2>Ticketinhaber [<a href='./galashow.csv'>csv</a>]</h2>
</div>
&euro; - gezahlt | CID - Convention ID, bei Conventiongängern
<br />
<br />
<?php 

echo "<form action='editg.php' name='update' method='post'>\n";
echo "  <table class='datatable'>\n";
echo "    <tr>";
echo "<td><b>&#9745;</b></td>";
echo "<td><b>ID</b></td>";
echo "<td><b>CID</b></td>";
echo "<td><b>Vorname</b></td>";
echo "<td><b>Nachname</b></td>";
echo "<td><b>&euro;</b></td>";
//echo "<td><b>ZIP</b></td>";
//echo "<td><b>IP</b></td>";
echo "    </tr>\n";
$csv = fopen("galashow.csv", "w");
fwrite($csv, "id,participant_id,prename,surname,birthday,zip,email,payed,regtime,arrivaltime,ip,browser,email_registered,email_ticket,active");
fwrite($csv, "\n");
$res = $DB->query("SELECT * FROM `galashow` WHERE `active` = 1;");
while ($data = $DB->fetch_assoc($res)) {
  // table
  echo "    <tr>";
  echo "<td><input type='checkbox' name='id_list[]' value='".$data['id']."' /></td>";
  echo "<td>".$data['id']."</td>";
  if (!is_null($data['participant_id'])) echo "<td>".$data['participant_id']."</td>";
  else echo "<td>&#x2718;</td>";
  echo "<td>".$data['prename']."</td>";
  echo "<td>".$data['surname']."</td>";
  if ($data['payed'])
    //echo "<td><a href='./editg.php?id=".$data['id']."&payed=false'>&#x2714;</a></td>";
    echo "<td>&#x2714;</td>";
  else 
    //echo "<td><a href='./editg.php?id=".$data['id']."&payed=true'>&#x2718;</a></td>";
    echo "<td>&#x2718;</td>";
  //echo "<td>".$data['zip']."</td>";
  //echo "<td>".$data['ip']."</td>";
  echo "    </tr>\n";
  // csv file
  fwrite($csv, $data['id']);
  fwrite($csv, ",");
  fwrite($csv, $data['participant_id']);
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
  fwrite($csv, $data['regtime']);
  fwrite($csv, ",");
  fwrite($csv, $data['arrivaltime']);
  fwrite($csv, ",");
  fwrite($csv, $data['ip']);
  fwrite($csv, ",");
  fwrite($csv, $data['browser']);
  fwrite($csv, ",");
  fwrite($csv, $data['email_registered']);
  fwrite($csv, ",");
  fwrite($csv, $data['email_ticket']);
  fwrite($csv, ",");
  fwrite($csv, $data['active']);
  fwrite($csv, "\n");
}
fclose($csv);
echo "  </table>\n";
?>
<div align='left' style='margin-left:5%;'>
  <br />
  <button name='update' type='submit' value='payed' class='button'>Selektion hat bezahlt</button>
  <br /><br />
  <button name='update' type='submit' value='notpayed' class='button'>Selektion hat <b>nicht</b> bezahlt</button>
  <br /><br />
  <button name='update' type='submit' value='delete' class='button' onclick='return confirm(\"Selektion wirklich löschen?\")'>
    Selektion <b>löschen</b>
  </button>
</div>
</form>
</body>
</html>
