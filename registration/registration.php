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
  <h2>15.-17. September 2017</h2>
  <h3>Registrierung</h3> 
</div>
<?php

if ( isset($_POST['reg']) ) {
  $new = true;
  $res = $DB->query("SELECT * FROM `participants`;");
  while ($data = $DB->fetch_assoc($res)) {
    if($data['prename'] == $DB->escape_string($_POST['prename'])
      && $data['surname'] == $DB->escape_string($_POST['surname']))
    {
      $new = false;
    }
  }

  if ($new 
      && $DB->escape_string($_POST['surname']) != ""
      && $DB->escape_string($_POST['prename']) != ""
      && $DB->escape_string($_POST['email']) != "") {
    $sql = "INSERT participants(";
    $sql .= "surname, prename, email, ip) VALUES (";
    $sql .= "'". $DB->escape_string($_POST['surname'])   ."', ";
    $sql .= "'". $DB->escape_string($_POST['prename'])   ."', ";
    $sql .= "'". $DB->escape_string($_POST['email'])     ."',";
    $sql .= "'". $_SERVER['REMOTE_ADDR']     ."');";

    $DB->query($sql);

?>
<div id='main'>
  <div class='center'>
    Angemeldet!
  </div>
</div>
<?php

  }
  else {

?>
<div id='main'>
  <div class='center'>
    Dieser Vor- und Nachname ist bereits registriert oder mindestens ein Feld ist leer!
  </div>
</div>
<?php

  }


}
else {

?>
<div id='main'>
  <form action='registration.php' name='reg' method='post'>
    <table align='center', class='registration'>
      <tr><td>Vorname</td><td><input type='text' name='prename' value='' maxlength='100' size='30' /></td></tr>
      <tr><td>Nachname</td><td ><input type='text' name='surname' value='' maxlength='100' size='30' /></td></tr>
      <tr><td>E-Mail</td><td ><input type='text' name='email' value='' maxlength='100' size='30' /></td></tr>
      <tr><td></td><td align='right'><input name='reg' type='submit' value='Anmelden' class='button' /></td></tr>
    </table>
  </form>
</div>
<?php

} 

?>
</body>
</html>
