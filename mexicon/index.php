<?php // index.php

session_start(); 

include("config/config.php");
include("inc/database.inc.php");
include("inc/validateDate.inc.php");
include("inc/getZip.inc.php");
include("inc/getAge.inc.php");

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
<link rel='stylesheet' type='text/css' href='./style.css' />
</head>
<body>
<div id='header'>
  <h1>6. Tübinger Jonglierconvention</h1>
  <h2>15.-17. September 2017</h2>
  <h3>Registrierung</h3> 
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
  $res = $DB->query("SELECT * FROM `person`;");
  while ($person = $DB->fetch_assoc($res)) {
    if($person['prename'] == $DB->escape_string($_POST['prename'])
      && $person['surname'] == $DB->escape_string($_POST['surname'])
      && $person['birthday'] == $DB->escape_string($_POST['birthday'])
      && $person['ip'] == $DB->escape_string($_SERVER['REMOTE_ADDR']))
    {
      $new = false;
      $sql = "UPDATE `convention` SET `active` = '1' WHERE `id` = '".$person['id']."';";
      $DB->query($sql);
      $sql = "UPDATE `galashow` SET `active` = '1' WHERE `id` = '".$person['id']."';";
      $DB->query($sql);
      $datetimenow = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone('UTC')); 
      $sql = "UPDATE `convention` SET `regtime` = '".$datetimenow->format("Y-m-d H:i:s")."' WHERE `id` = '".$person['id']."';";
      $DB->query($sql);
      $sql = "UPDATE `galashow` SET `regtime` = '".$datetimenow->format("Y-m-d H:i:s")."' WHERE `id` = '".$person['id']."';";
      $DB->query($sql);
    }
  }

  if (isset($_SESSION['captcha']) && strtolower($_POST['captcha']) == strtolower($_SESSION['captcha'])
      && validateDate($_POST['birthday'], 'Y-m-d')
      && $DB->escape_string($_POST['surname']) != ""
      && $DB->escape_string($_POST['prename']) != ""
      && $DB->escape_string($_POST['birthday']) != ""
      && $DB->escape_string($_POST['email']) != ""
      && filter_var($DB->escape_string($_POST['email']), FILTER_VALIDATE_EMAIL)
      && getAgeConvention($DB->escape_string($_POST['birthday'])) > 6) {
    if ($new) {
      // person table
      $sql = "INSERT person(";
      $sql .= "surname, prename, birthday, zip, email, ip, browser) VALUES (";
      $sql .= "'". $DB->escape_string($_POST['surname']) ."', ";
      $sql .= "'". $DB->escape_string($_POST['prename']) ."', ";
      $sql .= "'". $DB->escape_string($_POST['birthday']) ."',";
      $sql .= "'". getZip() ."',";
      $sql .= "'". $DB->escape_string($_POST['email']) ."',";
      $sql .= "'". $_SERVER['REMOTE_ADDR'] ."',";
      $sql .= "'". $_SERVER['HTTP_USER_AGENT'] ."');";
      $DB->query($sql);

      // fetch data from newly added person
      $res = $DB->query("SELECT * FROM `person`"
                        ." WHERE `surname` = '".$DB->escape_string($_POST['surname'])."'"
                        ." AND `prename` = '".$DB->escape_string($_POST['prename'])."'"
                        ." AND `birthday` = '".$DB->escape_string($_POST['birthday'])."'"
                        ." AND `ip` = '".$_SERVER['REMOTE_ADDR']."'"
                        ." LIMIT 1;");
      $person = $DB->fetch_assoc($res);

      // convention table
      $sql = "INSERT convention(";
      $sql .= "id, boat) VALUES (";
      $sql .= "'". $person['id'] ."', ";
      if (isset($_POST['boat'])) $sql .= "'1'";
      else $sql .= "'0'";
      $sql .= ");";
      $DB->query($sql);

      // galashow table
      $sql = "INSERT galashow(";
      $sql .= "id) VALUES (";
      $sql .= "'". $person['id'] ."'";
      $sql .= ");";
      $DB->query($sql);

    }

?>
<div id='main'>
  <div class='center'>
    Dein Conventionticket ist für dich reserviert. <br />
    Hinweis: Das Conventionticket ist gleichzeitig auch ein Ticket für die Galashow.
    <br />
    <br />
    <a href='<?php echo $_SERVER['PHP_SELF'] ?>'>Zum Registrierungsformular</a>
  </div>
</div>
<?php

  }
  else unset($_POST['captcha']);


}
if (!isset($_POST['reg']) || !isset($_POST['captcha'])) {
  if(isset($_POST['birthday'])) 
    $birthdayvalue = $_POST['birthday'];
  else
    $birthdayvalue = 'YYYY-MM-DD';

?>
<div id='main'>
  <div class='center'>
    Anzahl noch verfügbarer Tickets: <b> <?php echo $numfree; ?> </b> <br />
    <br />
<?php if(isset($_POST['reg'])) { ?>
    <font color='#ff0000'>Eingabe fehlerhaft!</font>
    <br />
    Datumsformat: YYYY-MM-DD, z.B. 1999-01-28 für 28. Januar 1999. <br />
    Eingabe des Sicherheitscodes nicht vergessen! <br />
    Du musst über 6 Jahre alt sein! <br />
    <br />
<?php } ?>
  </div>
  <form action='<?php echo $_SERVER['REQUEST_URI']; ?>' name='reg' method='post' class='left'>
    <table align='center' class='registration'>
      <tr><td>Vorname</td><td><input type='text' name='prename' value='<?php if(isset($_POST['prename'])) echo $_POST['prename']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>Nachname</td><td><input type='text' name='surname' value='<?php if(isset($_POST['surname'])) echo $_POST['surname']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>Geburtstag</td><td><input type='text' name='birthday' value='<?php echo $birthdayvalue; ?>' maxlength='10' size='30' /></td></tr>
      <tr><td>E-Mail</td><td><input type='text' name='email' value='<?php if(isset($_POST['email'])) echo $_POST['email']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>&#9972;</td><td ><input type='checkbox' name='boat' <?php if (isset($_POST['boat'])) echo "checked"; ?> /></td></tr>
      <tr><td><img src='captcha/captcha.php' class='captcha' border='0' alt='Sicherheitscode' title='Sicherheitscode' height='25px'></td><td><input type='text' name='captcha' value='' maxlength='5' size='30' /></td></tr>
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
