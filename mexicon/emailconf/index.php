<?php
# Just for testing:
namespace Jonglaria;

require_once("/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/dynamic/ticket.php");

$ticket = new Ticket;

if($ticket->checkMail(urldecode($_GET["mail"]), $_GET["check"]))
{
	print "<p>Versuche Emailversand</p>";
	if($ticket->sendPaymentMail($_GET["id"]))
	{
		print "<p>Email mit Informationen zur Bezahlung wurde rausgesendet.</p>";
	}
	else
	{
		print "<p>Problem beim Emailversand</p>";
	}
}
else
{
	print "<p>Email ".$_GET["mail"]." (id=".$_GET["id"].") ungültig</p>";
}

?>
