<!DOCTYPE html 
     PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'
     'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='de' lang='de'>
<head>
<meta http-equiv='content-type' content='text/html; charset=utf-8'/>
<title>Jonglaria</title>
<link rel='stylesheet' type='text/css' href='../style.css' />
</head>
<body>
<div id='header'>
  <h1>6. Tübinger Jonglierconvention</h1>
  <h2>16. September 2017</h2>
  <h3>Tickets</h3> 
</div>
<?php

require_once("/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/dynamic/ticket.php");

$ticket = new Jonglaria\Ticket;

if($ticket->checkMail(urldecode($_GET["mail"]), $_GET["check"]))
{
	if($ticket->sendPaymentMail($_GET["id"]))
	{
		print "<div id='main'><div id='center'>Email mit Informationen zur Bezahlung fuer id ".$_GET["id"]." wurde rausgesendet.</div></div>";
	}
	else
	{
		print "<div id='main'><div id='center'>Oh, da ist etwas schiefgegangen beim Emailversand! Das tut uns leid! Bitte kontaktiere uns unter reghelp@jonglaria.org</div></div>";
	}
}
else
{
	print "<div id='main'><div id='center'>Emaillink für mail ".$_GET["mail"]." (id=".$_GET["id"].") ungültig. Hast du vielleicht den Link kopiert und einen Fehler gemacht? Dann kontaktiere uns bitte unter reghelp@jonglaria.org</div></div>";
}

?>
</body>
</html>
