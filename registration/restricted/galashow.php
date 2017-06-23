<?php

include("../config/config.php");
include("../inc/database.inc.php");
include("../inc/getAge.inc.php");

$DB = new Database("mexicon");  

$res = $DB->query("SELECT * FROM `galashow` WHERE `active` = 1;");
$numreg = $DB->num_rows($res);
$numfree = GALASLOTS - $numreg;

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
<div id='main' class='center'>
CID - Convention ID, bei Conventiongängern | &#9960; - Ticketanzahl | &#9889; - Alter bei der Galashow | &euro; - gezahlt | &#9977; - Eingecheckt
<br /> <br />
&#35; <?php echo $numreg; ?>
<br /> <br />
<?php 

echo "<form action='editg.php' name='update' method='post'>\n";
echo "  <table class='datatable'>\n";
echo "    <tr>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."'>&#9745;</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=id'>ID</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=participant_id'>CID</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=ticketcount'>&#9960;</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=prename'>Vorname</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=surname'>Nachname</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=birthday'>&#9889;</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=payed'>&euro;</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=arrivaltime'>&#9977;</a></b></td>";
//echo "<td><b>ZIP</b></td>";
//echo "<td><b>IP</b></td>";
echo "    </tr>\n";
$csv = fopen("galashow.csv", "w");
fwrite($csv, "id,participant_id,prename,surname,birthday,zip,email,payed,regtime,arrivaltime,ip,browser,email_registered,email_ticket,active");
fwrite($csv, "\n");
if (isset($_GET['orderby'])) 
  $res = $DB->query("SELECT * FROM `galashow` WHERE `active` = 1 ORDER BY `".$DB->escape_string($_GET['orderby'])."` ASC;");
else
  $res = $DB->query("SELECT * FROM `galashow` WHERE `active` = 1 ORDER BY `regtime` ASC, `surname` ASC, `prename` ASC;");
while ($data = $DB->fetch_assoc($res)) {
  // table
  echo "    <tr>";
  echo "<td><input type='checkbox' name='id_list[]' value='".$data['id']."' /></td>";
  echo "<td>".$data['id']."</td>";
  if (!is_null($data['participant_id'])) echo "<td>".$data['participant_id']."</td>";
  else echo "<td>&#x2718;</td>";
  echo "<td>".$data['ticketcount']."</td>";
  echo "<td>".$data['prename']."</td>";
  echo "<td>".$data['surname']."</td>";
  echo "<td>".getAgeGala($data['birthday'])."</td>";
  if ($data['payed'])
    //echo "<td><a href='./editg.php?id=".$data['id']."&payed=false'>&#x2714;</a></td>";
    echo "<td>&#x2714;</td>";
  else 
    //echo "<td><a href='./editg.php?id=".$data['id']."&payed=true'>&#x2718;</a></td>";
    echo "<td>&#x2718;</td>";
  if (!is_null($data['arrivaltime'])) echo "<td>&#x2714;</td>";
  else echo "<td>&#x2718;</td>";
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
<div class='buttonfield'>
  <br />
  <button name='update' type='submit' value='payed' class='button'>Selektion hat bezahlt</button>
  <br /><br />
  <button name='update' type='submit' value='arrived' class='button'>Selektion ist angekommen</button>
  <br /><br />
  <br />
  <button name='update' type='submit' value='notpayed' class='button'>Selektion hat <b>nicht</b> bezahlt</button>
  <br /><br />
  <button name='update' type='submit' value='notarrived' class='button'>Selektion ist <b>nicht</b> angekommen</button>
  <br /><br />
  <br />
  <button name='update' type='submit' value='delete' class='button' onclick='return confirm(\"Selektion wirklich löschen?\")'>
    Selektion <b>löschen</b>
  </button>
</div>
</div>
</form>
</body>
</html>
