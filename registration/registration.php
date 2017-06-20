<?php

include("config/config.php");
include("inc/database.inc.php");
include("inc/validateDate.inc.php");
include("inc/getZip.inc.php");

$DB = new Database("mexicon");  

$res = $DB->query("SELECT * FROM `participants` WHERE `active` = 1;");
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
<link rel='stylesheet' type='text/css' href='./registration.css' />
</head>
<body>
<div id='header'>
  <h1>6. Tübinger Jonglierconvention</h1>
  <h2>15.-17. September 2017</h2>
  <h3>&#9885; Registrierung &#9885;</h3> 
</div>
<?php

if ($numfree <= 0) {

?>
<div id='main'>
  <div class='center'>
    Registrierung geschlossen, alle <?php echo $numreg; ?> Tickets sind vergeben!
  </div>
</div>
</body>
</html>
<?php

  exit();

}
if ( isset($_POST['reg']) ) {
  $new = true;
  $res = $DB->query("SELECT * FROM `participants`;");
  while ($data = $DB->fetch_assoc($res)) {
    if($data['prename'] == $DB->escape_string($_POST['prename'])
      && $data['surname'] == $DB->escape_string($_POST['surname'])
      && $data['birthday'] == $DB->escape_string($_POST['birthday'])
      && $data['ip'] == $DB->escape_string($_SERVER['REMOTE_ADDR']))
    {
      $new = false;
    }
  }

  if ($new 
      && validateDate($_POST['birthday'], 'Y-m-d')
      && $DB->escape_string($_POST['surname']) != ""
      && $DB->escape_string($_POST['prename']) != ""
      && $DB->escape_string($_POST['birthday']) != ""
      && $DB->escape_string($_POST['email']) != "") {
    // participants table
    $sql = "INSERT participants(";
    $sql .= "surname, prename, birthday, zip, email, boat, ip, browser) VALUES (";
    $sql .= "'". $DB->escape_string($_POST['surname']) ."', ";
    $sql .= "'". $DB->escape_string($_POST['prename']) ."', ";
    $sql .= "'". $DB->escape_string($_POST['birthday']) ."',";
    $sql .= "'". getZip() ."',";
    $sql .= "'". $DB->escape_string($_POST['email']) ."',";
    if (isset($_POST['boat'])) $sql .= "'1',";
    else $sql .= "'0',";
    $sql .= "'". $_SERVER['REMOTE_ADDR'] ."',";
    $sql .= "'". $_SERVER['HTTP_USER_AGENT'] ."');";

    $DB->query($sql);

    // galashow table
    $res = $DB->query("SELECT * FROM `participants` WHERE 
                       `surname` =  '".$DB->escape_string($_POST['surname'])."' AND
                       `prename` =  '".$DB->escape_string($_POST['prename'])."' AND
                       `birthday` = '".$DB->escape_string($_POST['birthday'])."' AND
                       `ip` =       '".$_SERVER['REMOTE_ADDR']."'
                       LIMIT 1;");
    $data = $DB->fetch_assoc($res);
    $sql = "INSERT galashow(";
    $sql .= "participant_id, surname, prename, birthday, zip, email, ip, browser) VALUES (";
    $sql .= "'". $data['id'] ."', ";
    $sql .= "'". $DB->escape_string($_POST['surname']) ."', ";
    $sql .= "'". $DB->escape_string($_POST['prename']) ."', ";
    $sql .= "'". $DB->escape_string($_POST['birthday']) ."',";
    $sql .= "'". getZip() ."',";
    $sql .= "'". $DB->escape_string($_POST['email']) ."',";
    $sql .= "'". $_SERVER['REMOTE_ADDR'] ."',";
    $sql .= "'". $_SERVER['HTTP_USER_AGENT'] ."');";

    $DB->query($sql);

?>
<div id='main'>
  <div class='center'>
    Angemeldet!
    <br />
    <br />
    <a href='.'>Zurück zum Registrierungsformular</a>
  </div>
</div>
<?php

  }
  else {

?>
<div id='main'>
  <div class='center'>
    Anzahl noch verfügbarer Tickets: <b> <?php echo $numfree; ?> </b> <br />
    <br />
    <font color='#ff0000'>Eingabe fehlerhaft oder bereits registriert!</font>
    <br />
    Datumsformat: YYYY-MM-DD, z.B. 1999-01-28 für 28. Januar 1999
    <br />
    <br />
  </div>
  <form action='registration.php' name='reg' method='post'>
    <table align='center' class='registration'>
    <tr><td>Vorname</td><td><input type='text' name='prename' value='<?php echo $_POST['prename']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>Nachname</td><td ><input type='text' name='surname' value='<?php echo $_POST['surname']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>Geburtstag</td><td ><input type='text' name='birthday' value='<?php echo $_POST['birthday']; ?>' maxlength='10' size='30' /></td></tr>
      <tr><td>E-Mail</td><td ><input type='text' name='email' value='<?php echo $_POST['email']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>&#9972;</td><td ><input type='checkbox' name='boat' <?php if (isset($_POST['boat'])) echo "checked"; ?> /></td></tr>
      <tr><td></td><td align='right'><input name='reg' type='submit' value='Anmelden' class='button' /></td></tr>
    </table>
  </form>
</div>
<br /> <br /> &#9972; - Interesse an der Stocherkahnfahrt am Sonntag teilzunehmen?
<?php

  }


}
else {

?>
<div id='main'>
  <div class='center'>
    Anzahl noch verfügbarer Tickets: <b> <?php echo $numfree; ?> </b> <br />
    <br />
  </div>
  <form action='registration.php' name='reg' method='post'>
    <table align='center' class='registration'>
      <tr><td>Vorname</td><td><input type='text' name='prename' value='' maxlength='100' size='30' /></td></tr>
      <tr><td>Nachname</td><td ><input type='text' name='surname' value='' maxlength='100' size='30' /></td></tr>
      <tr><td>Geburtstag</td><td ><input type='text' name='birthday' value='YYYY-MM-DD' maxlength='10' size='30' /></td></tr>
      <tr><td>E-Mail</td><td ><input type='text' name='email' value='' maxlength='100' size='30' /></td></tr>
      <tr><td>&#9972;</td><td ><input type='checkbox' name='boat' /></td></tr>
      <tr><td></td><td align='right'><input name='reg' type='submit' value='Anmelden' class='button' /></td></tr>
    </table>
  </form>
</div>
<br /> <br /> &#9972; - Interesse an der Stocherkahnfahrt am Sonntag teilzunehmen?
<?php

} 

?>
</body>
</html>
