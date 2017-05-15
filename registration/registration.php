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
</head>
<body>
<?php

if ( isset($_POST['reg']) ) {
  $sql = "INSERT participants(";
  $sql .= "surname, prename, email) VALUES (";
  $sql .= "'". $_POST['surname']   ."', ";
  $sql .= "'". $_POST['prename']   ."', ";
  $sql .= "'". $_POST['email']     ."');";

  $DB->query($sql);

  echo "Angemeldet!";
  echo "\n";
}
else {

?>
<form action='registration.php' name='reg' method='post'>
  <table>
    <tr><td>Vorname</td><td><input type='text' name='prename' value='' maxlength='100' size='20' /></td></tr>
    <tr><td>Nachname</td><td ><input type='text' name='surname' value='' maxlength='100' size='20' /></td></tr>
    <tr><td>E-Mail</td><td ><input type='text' name='email' value='' maxlength='100' size='20' /></td></tr>
    <tr><td></td><td align='right'><input name='reg' type='submit' value='Anmelden' style='background:#c0c0c0;border:0px;' /></td></tr>
  </table>
</form>
<?php

} 

?>
</body>
</html>
