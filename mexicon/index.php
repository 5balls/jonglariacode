<?php 

session_start(); 

require_once("config/config.php");
require_once("inc/database.inc.php");
require_once("inc/validateDate.inc.php");
require_once("inc/getZip.inc.php");
require_once("inc/getAge.inc.php");
require_once("/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/dynamic/ticket.php");

$DB = new Database("db1110266-jonglaria");  
$ticket = new Jonglaria\Ticket;

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
      && validateDate($DB->escape_string($_POST['birthday']), 'Y-m-d')
      && $DB->escape_string($_POST['surname']) != ""
      && $DB->escape_string($_POST['prename']) != ""
      && $DB->escape_string($_POST['email']) != ""
      && filter_var($DB->escape_string($_POST['email']), FILTER_VALIDATE_EMAIL)
      && (getAgeConvention($DB->escape_string($_POST['birthday'])) >= 18
        || (getAgeConvention($DB->escape_string($_POST['birthday'])) > 6 
          && $DB->escape_string($_POST['cg_surname']) != "" 
          && $DB->escape_string($_POST['cg_prename']) != "" 
          && validateDate($DB->escape_string($_POST['cg_birthday']), 'Y-m-d')
          && getAgeConvention($DB->escape_string($_POST['cg_birthday'])) >= 18 ) ) ) {
    if ($new) {
      // person table
      $sql = "INSERT person(";
      $sql .= "surname, prename, birthday, zip, email, ip, browser) VALUES (";
      $sql .= "'". $DB->escape_string($_POST['surname']) ."', ";
      $sql .= "'". $DB->escape_string($_POST['prename']) ."', ";
      $sql .= "'". $DB->escape_string($_POST['birthday']) ."',";
      if (is_null(getZip())) $sql .= "NULL,";
      else $sql .= "'". getZip() ."',";
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

      // caregiver table
      if ($DB->escape_string($_POST['cg_surname']) != ""
          && $DB->escape_string($_POST['cg_prename']) != ""
          && validateDate($DB->escape_string($_POST['cg_birthday']), 'Y-m-d') ) {
        $res_cg = $DB->query("SELECT * FROM `person`"
                          ." WHERE `surname` = '".$DB->escape_string($_POST['cg_surname'])."'"
                          ." AND `prename` = '".$DB->escape_string($_POST['cg_prename'])."'"
                          ." AND `birthday` = '".$DB->escape_string($_POST['cg_birthday'])."'"
                          ." AND `ip` = '".$_SERVER['REMOTE_ADDR']."'"
                          ." LIMIT 1;");
        if (!($caregiver = $DB->fetch_assoc($res_cg))) $caregiver = null;
        $sql = "INSERT caregiver(";
        $sql .= "id, ";
        if(!is_null($caregiver)) $sql .= "caregiver_id, ";
        $sql .= "surname, prename, birthday) VALUES (";
        $sql .= "'". $person['id'] ."', ";
        if(!is_null($caregiver)) $sql .= "'".$caregiver['id']."', ";
        $sql .= "'". $DB->escape_string($_POST['cg_surname']) ."', ";
        $sql .= "'". $DB->escape_string($_POST['cg_prename']) ."', ";
        $sql .= "'". $DB->escape_string($_POST['cg_birthday']) ."');";
        $DB->query($sql);
      }

      // send confirmation email
      $ticket->sendConfirmationMail($person['id']);
    }

?>
<div id='main'>
  <div class='center'>
    Wir haben dir eine Email geschickt mit einem Link zur Bestätigung deiner Emailadresse. <br />
    Solltest du keine Email erhalten haben und sicher sein, keinen Tippfehler gemacht zu haben, kontaktiere uns bitte unter reghelp@jonglaria.org
    <br />
    <br />
    <a href='<?php echo $_SERVER['REQUEST_URI'] ?>'>Zum Registrierungsformular</a>
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
  if(isset($_POST['cg_birthday'])) 
    $cg_birthdayvalue = $_POST['cg_birthday'];
  else
    $cg_birthdayvalue = 'YYYY-MM-DD';

?>
<div id='main'>
  <div class='center'>
    Anzahl noch verfügbarer Tickets: <b> <?php echo $numfree; ?> </b> <br />
<table class='datatable'>
<tr><th>Alter bei Convention</th><th>Conventionpreis (inklusive Galashow) bei Vorüberweisung</th></tr>
<tr><td>jünger als 6 Jahre</td><td>kostenlos!</td></tr>
<tr><td>6 bis 11 Jahre</td><td>1 Euro pro Lebensjahr</td></tr>
<tr><td>12 bis 17 Jahre</td><td>20 Euro</td></tr>
<tr><td>ab 18 Jahre</td><td>28 Euro</td></tr>
</table>
    <br />
<?php if(isset($_POST['reg'])) { ?>
    <font color='#ff0000'>Achtung!</font>
    <br />
<?php   if (validateDate($_POST['birthday'], 'Y-m-d')) { ?>
<?php     if(getAgeConvention($DB->escape_string($_POST['birthday'])) < 6) { ?>
    Du musst über 6 Jahre alt sein um dich für die Convention zu registrieren! 
    Bist Du unter 6 Jahre alt ist der Eintritt frei und deswegen keine Registrierung notwendig. <br />
<?php     } else if(getAgeConvention($DB->escape_string($_POST['birthday'])) < 18) { ?>
<?php       $youth=true; ?>
    Du bist unter 18 und musst deshalb eine Bezugsperson angeben 
    und einen <i>Muttizettel</i> ausgefüllt zur Convention mitbrigen! <br />
<?php     } ?> 
<?php   } ?> 
    <br />
    Alle Felder müssen ausgefüllt sein. <br />
    Datumsformat: YYYY-MM-DD, z.B. 1999-01-28 für 28. Januar 1999. <br />
    Eingabe des Sicherheitscodes nicht vergessen! <br />
    <br />
<?php } ?>
  </div>
  <form action='<?php echo $_SERVER['REQUEST_URI']; ?>' name='reg' method='post' class='left'>
    <table align='center' class='registration'>
      <tr><td>Vorname</td><td><input type='text' name='prename' value='<?php if(isset($_POST['prename'])) echo $_POST['prename']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>Nachname</td><td><input type='text' name='surname' value='<?php if(isset($_POST['surname'])) echo $_POST['surname']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>Geburtstag</td><td><input type='text' name='birthday' value='<?php echo $birthdayvalue; ?>' maxlength='10' size='30' /></td></tr>
      <tr><td>E-Mail</td><td><input type='text' name='email' value='<?php if(isset($_POST['email'])) echo $_POST['email']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>&#9972; Interesse Stocherkahnfahrt?</td><td ><input type='checkbox' name='boat' <?php if (isset($_POST['boat'])) echo "checked"; ?> /></td></tr>
<?php if(isset($youth)) { ?>
      <tr><td>&#9937; Vorname</td><td><input type='text' name='cg_prename' value='<?php if(isset($_POST['cg_prename'])) echo $_POST['cg_prename']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>&#9937; Nachname</td><td><input type='text' name='cg_surname' value='<?php if(isset($_POST['cg_surname'])) echo $_POST['cg_surname']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>&#9937; Geburtstag</td><td><input type='text' name='cg_birthday' value='<?php echo $cg_birthdayvalue; ?>' maxlength='10' size='30' /></td></tr>
<?php } else { ?>
      <input type='hidden' name='cg_prename' value='' />
      <input type='hidden' name='cg_surname' value='' />
      <input type='hidden' name='cg_birthday' value='YYYY-MM-DD' />
<?php } ?>
      <tr><td><img src='captcha/captcha.php' class='captcha' border='0' alt='Sicherheitscode' title='Sicherheitscode' height='25px'></td><td><input type='text' name='captcha' value='' maxlength='5' size='30' /></td></tr>
      <tr><td></td><td align='right'><input name='reg' type='submit' value='Anmelden' class='button' /></td></tr>
    </table>
  </form>
</div>
<br /> <br /> &#9972; - Interesse an der Stocherkahnfahrt am Sonntag teilzunehmen?
<?php if(isset($youth)) { ?>
<br /> &#9937; - Bezugsperson (Fülle in diese Felder den Vor-, Nachname und Geburtstag deiner Bezugsperson)
<?php } ?>
<?php

} 

?>
</body>
</html>
