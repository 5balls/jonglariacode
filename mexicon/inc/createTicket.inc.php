<?php // createTicket.inc.php
// Not strictly needed anymore because function could be called directly instead but whatever

require_once("/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/dynamic/ticket.php");

function createConventionTicket($id,$DB) {
	$ticket = new Jonglaria\Ticket;
	$ticket->sendElectronicTicket($id);
}

function createGalaTicket($id,$DB) {
	$ticket = new Jonglaria\Ticket;
	$ticket->sendElectronicTicketGala($id);
}


?>
