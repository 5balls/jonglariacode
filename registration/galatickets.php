<?php // galatickets.php

session_start(); 

include("config/config.php");
include("inc/database.inc.php");
include("inc/validateDate.inc.php");
include("inc/getZip.inc.php");

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
<link rel='stylesheet' type='text/css' href='./style.css' />
</head>
<body>
<div id='header'>
  <h1>Galashow der 6. Tübinger Jonglierconvention</h1>
  <h2>16. September 2017</h2>
  <h3>Tickets</h3> 
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
  $res = $DB->query("SELECT * FROM `galashow`;");
  while ($data = $DB->fetch_assoc($res)) {
    if($data['prename'] == $DB->escape_string($_POST['prename'])
      && $data['surname'] == $DB->escape_string($_POST['surname'])
      && $data['birthday'] == $DB->escape_string($_POST['birthday'])
      && $data['ip'] == $DB->escape_string($_SERVER['REMOTE_ADDR']))
    {
      $new = false;
      $sql = "UPDATE `galashow` SET `active` = '1' WHERE `id` = '".$data['id']."';";
      $DB->query($sql);
      $datetimenow = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone('UTC')); 
      $sql = "UPDATE `galashow` SET `regtime` = '".$datetimenow->format("Y-m-d H:i:s")."' WHERE `id` = '".$data['id']."';";
      $DB->query($sql);
    }
  }

  if (isset($_SESSION['captcha']) && $_POST['captcha'] == $_SESSION['captcha'] 
      && validateDate($_POST['birthday'], 'Y-m-d')
      && $DB->escape_string($_POST['surname']) != ""
      && $DB->escape_string($_POST['prename']) != ""
      && $DB->escape_string($_POST['birthday']) != ""
      && $DB->escape_string($_POST['email']) != ""
      && filter_var($DB->escape_string($_POST['email']), FILTER_VALIDATE_EMAIL)) {
    if($new) {
      // galashow table
      $sql = "INSERT galashow(";
      $sql .= "surname, prename, birthday, zip, email, ip, browser) VALUES (";
      $sql .= "'". $DB->escape_string($_POST['surname']) ."', ";
      $sql .= "'". $DB->escape_string($_POST['prename']) ."', ";
      $sql .= "'". $DB->escape_string($_POST['birthday']) ."',";
      $sql .= "'". getZip() ."',";
      $sql .= "'". $DB->escape_string($_POST['email']) ."',";
      $sql .= "'". $_SERVER['REMOTE_ADDR'] ."',";
      $sql .= "'". $_SERVER['HTTP_USER_AGENT'] ."');";

      $DB->query($sql);
    }

?>
<div id='main'>
  <div class='center'>
    Dein Ticket für die Galashow ist reserviert.
    <br />
    <br />
    <a href='<?php echo $_SERVER['PHP_SELF'] ?>'>Zum Ticketformular</a>
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
    <br />
<?php } ?>
  </div>
  <form action='galatickets.php' name='reg' method='post'>
    <table align='center' class='galatickets'>
    <tr><td>Vorname</td><td><input type='text' name='prename' value='<?php if(isset($_POST['prename'])) echo $_POST['prename']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>Nachname</td><td ><input type='text' name='surname' value='<?php if(isset($_POST['surname'])) echo $_POST['surname']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td>Geburtstag</td><td ><input type='text' name='birthday' value='<?php echo $birthdayvalue; ?>' maxlength='10' size='30' /></td></tr>
      <tr><td>E-Mail</td><td ><input type='text' name='email' value='<?php if(isset($_POST['email'])) echo $_POST['email']; ?>' maxlength='100' size='30' /></td></tr>
      <tr><td><img src='captcha/captcha.php' class='captcha' border='0' alt='Sicherheitscode' title='Sicherheitscode' height='25px'></td><td ><input type='text' name='captcha' value='' maxlength='5' size='30' /></td></tr>
      <tr><td></td><td align='right'><input name='reg' type='submit' value='Ticket reservieren' class='button' /></td></tr>
    </table>
  </form>
</div>
<?php

} 

?>
</body>
</html>
