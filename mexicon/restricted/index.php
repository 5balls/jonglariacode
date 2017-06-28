<?php

include("../config/config.php");
include("../inc/database.inc.php");
include("../inc/printMenu.inc.php");
include("../inc/getAge.inc.php");
include("../inc/printBoolValue.inc.php");

$DB = new Database("mexicon");  

$res = $DB->query("SELECT * FROM `convention` WHERE `active` = 1;");
$numreg = $DB->num_rows($res);
$numfree = SLOTS - $numreg;

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
<?php echo printMenu(); ?>
<div id='header'>
  <h1>6. Tübinger Jonglierconvention</h1>
  <h2>Teilnehmerliste [<a href='./convention.csv'>csv</a>]</h2>
</div>
<div id='main' class='center'>
&#9889; - Alter bei Beginn der Convention | &euro; - gezahlt | &#9972; - Stocherkahn Interesse | &#9977; - Eingecheckt
<br /> <br />
&#35; <?php echo $numreg; ?>
<br /> <br />
<?php 

echo "<form action='editc.php' name='update' method='post'>\n";
echo "  <table class='datatable'>\n";
echo "    <tr>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."'>&#9745;</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=id'>ID</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=prename'>Vorname</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=surname'>Nachname</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=birthday'>&#9889;</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=payed'>&euro;</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=boat'>&#9972;</a></b></td>";
echo "<td><b><a href='".$_SERVER['PHP_SELF']."?orderby=arrivaltime'>&#9977;</a></b></td>";
echo "<td><b>Bezugsperson</b></td>";
//echo "<td><b>ZIP</b></td>";
//echo "<td><b>IP</b></td>";
echo "    </tr>\n";
$csv = fopen("convention.csv", "w");
fwrite($csv, "id,prename,surname,birthday,zip,email,payed,boat,regtime,arrivaltime,ip,browser,email_registered,email_ticket,active,caregiver_prename,caregiver_surname,caregiver_birthday");
fwrite($csv, "\n");
if (isset($_GET['orderby'])) 
  $res = $DB->query("SELECT * FROM `person` JOIN `convention` ON `person`.`id` = `convention`.`id` "
                   ."ORDER BY `".$DB->escape_string($_GET['orderby'])."` ASC;");
else
  $res = $DB->query("SELECT * FROM `person` JOIN `convention` ON `person`.`id` = `convention`.`id` "
                   ."ORDER BY `regtime` ASC, `surname` ASC, `prename` ASC;");
while ($person = $DB->fetch_assoc($res)) {
  $res_cg = $DB->query("SELECT * FROM `caregiver` WHERE `id` = '".$person['id']."'");
  if(!($caregiver = $DB->fetch_assoc($res_cg))) $caregiver=null;
  if($person['active'] == 1) {
    // table
    echo "    <tr>";
    echo "<td><input type='checkbox' name='id_list[]' value='".$person['id']."' /></td>";
    echo "<td>".$person['id']."</td>";
    echo "<td>".$person['prename']."</td>";
    echo "<td>".$person['surname']."</td>";
    echo "<td>".getAgeConvention($person['birthday'])."</td>";
    echo "<td>".printBoolValue($person['payed'])."</td>";
    echo "<td>".printBoolValue($person['boat'])."</td>";
    if (!is_null($person['arrivaltime'])) echo "<td>&#x2714;</td>";
    else echo "<td>&#x2718;</td>";
    //echo "<td>".$person['zip']."</td>";
    //echo "<td>".$person['ip']."</td>";
    if(is_null($caregiver)) echo "<td>&ndash;</td>";
    else echo "<td>"
      .printBoolValue($caregiver['verified'])." "
      .$caregiver['prename']." ".$caregiver['surname']." (".getAgeConvention($caregiver['birthday']).")"
      ."</td>";
    echo "    </tr>\n";
  }
  // csv file
  fwrite($csv, $person['id']);
  fwrite($csv, ",");
  fwrite($csv, $person['prename']);
  fwrite($csv, ",");
  fwrite($csv, $person['surname']);
  fwrite($csv, ",");
  fwrite($csv, $person['birthday']);
  fwrite($csv, ",");
  fwrite($csv, $person['zip']);
  fwrite($csv, ",");
  fwrite($csv, $person['email']);
  fwrite($csv, ",");
  fwrite($csv, $person['payed']);
  fwrite($csv, ",");
  fwrite($csv, $person['boat']);
  fwrite($csv, ",");
  fwrite($csv, $person['regtime']);
  fwrite($csv, ",");
  fwrite($csv, $person['arrivaltime']);
  fwrite($csv, ",");
  fwrite($csv, $person['ip']);
  fwrite($csv, ",");
  fwrite($csv, $person['browser']);
  fwrite($csv, ",");
  fwrite($csv, $person['email_registered']);
  fwrite($csv, ",");
  fwrite($csv, $person['email_ticket']);
  fwrite($csv, ",");
  fwrite($csv, $person['active']);
  fwrite($csv, ",");
  if(is_null($caregiver)) fwrite($csv, "");
  else fwrite($csv, $caregiver['prename']);
  fwrite($csv, ",");
  if(is_null($caregiver)) fwrite($csv, "");
  else fwrite($csv, $caregiver['surname']);
  fwrite($csv, ",");
  if(is_null($caregiver)) fwrite($csv, "");
  else fwrite($csv, $caregiver['birthday']);
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
  <button name='update' type='submit' value='verified' class='button'>Selektion: Bezugsperson bestätigen</button>
  <br /><br />
  <br />
  <button name='update' type='submit' value='notpayed' class='button'>Selektion hat <b>nicht</b> bezahlt</button>
  <br /><br />
  <button name='update' type='submit' value='notarrived' class='button'>Selektion ist <b>nicht</b> angekommen</button>
  <br /><br />
  <button name='update' type='submit' value='notverified' class='button'>Selektion: Bezugsperson <b>nicht</b> bestätigen</button>
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
