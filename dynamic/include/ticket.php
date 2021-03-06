<?php
# Class for sending emails
namespace Jonglaria;

require_once("ticketdb.php");
class Ticket
{
	private $salt = "ksajdfz26kadfls8";
	private $conf_salt = "ölrka2139asduc";
	private $pay_salt = "iwebrkc-7391mql";
	private	$tdb = "";
	public function __construct()
	{
		$this->tdb = new TicketDatabase();
	}

	public function createUniqueIdentifier($id)
	{
		return substr(md5($id.$this->salt),1,8);
	}
	public function createUniqueIdentifierPayment($id)
	{
		return substr(md5($id.$this->pay_salt),1,8);
	}
	public function createUniqueIdentifierConfirmation($mail_address)
	{
		return substr(md5($mail_address.$this->conf_salt),1,8);
	}
	// Remember to urldecode mail before using function
	public function checkMail($mail_address, $check)
	{
		if($this->createUniqueIdentifierConfirmation(trim($mail_address)) == $check)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	public function utf8base64encode($string)
	{
		return "=?utf-8?B?".base64_encode($string)."?=";
	}
	public function sendConfirmationMail($id)
	{
		$eol = "\r\n";
		$mail_address = $this->tdb->getEmail($id);
		$regcode = $this->createUniqueIdentifierConfirmation(trim($mail_address));
		$this->tdb->insertRegCode($regcode, $id);
		$headers = "From: Mexicon <registration@jonglaria.org>".$eol;
		$headers .= "Cc: regcc@jonglaria.org".$eol;
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
		$headers .= "Content-Transfer-Encoding: 8bit".$eol;
		$subject = "Mexicon - Bestätigung Emailadresse";
		$subject = $this->utf8base64encode($subject);
		$body .= "Hallo ".$this->tdb->getFirstName($id)." ".$this->tdb->getFamilyName($id).",".$eol.$eol;
		$body .= "wir freuen uns über dein Interesse an der Mexicon, der 6. Tübinger Jonglierconvention am 15.9. bis 17.9.!".$eol.$eol;
		$body .= "Damit wir wissen, dass du dich bei der Emailadresse nicht vertippt hast klicke bitte auf folgenden Bestätigunglink, wir werden dir anschließend eine Email mit den Überweisungsinformationen zuschicken:".$eol.$eol;
		$body .= "https://jonglaria.org/mexicon/emailconf/";
		$body .= "?mail=".urlencode(trim($mail_address));
		$body .= "&id=".urlencode($id);
		$body .= "&check=".urlencode($regcode).$eol.$eol;
		$body .= "Wir freuen uns auf dich,".$eol.$eol;
		$body .= "i.A. Jonglaria e.V.".$eol;
		return mail($mail_address,$subject,$body,$headers, "-f registration@jonglaria.org");
	}
	public function sendConfirmationMailGala($id)
	{
		$eol = "\r\n";
		$mail_address = $this->tdb->getEmailGala($id);
		$regcode = $this->createUniqueIdentifierConfirmation(trim($mail_address));
		$this->tdb->insertRegCodeGala($regcode, $id);
		$headers = "From: \"".$this->utf8base64encode("Tübinger Fröschle")."\" <registration@jonglaria.org>".$eol;
		$headers .= "Cc: regcc@jonglaria.org".$eol;
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
		$headers .= "Content-Transfer-Encoding: 8bit".$eol;
		$subject = "Tübinger Fröschle - Bestätigung Emailadresse";
		$subject = $this->utf8base64encode($subject);
		$body .= "Hallo ".$this->tdb->getFirstNameGala($id)." ".$this->tdb->getFamilyNameGala($id).",".$eol.$eol;
		$body .= "wir freuen uns über dein Interesse an dem Adi Pius Kläger Kleinkunstpreis Tübinger Fröschle am 16.9.!".$eol.$eol;
		$body .= "Damit wir wissen, dass du dich bei der Emailadresse nicht vertippt hast klicke bitte auf folgenden Bestätigunglink, wir werden dir anschließend eine Email mit den Überweisungsinformationen zuschicken:".$eol.$eol;
		$body .= "https://jonglaria.org/mexicon/gala/emailconf/";
		$body .= "?mail=".urlencode(trim($mail_address));
		$body .= "&id=".urlencode($id);
		$body .= "&check=".urlencode($regcode).$eol.$eol;
		$body .= "Wir freuen uns auf dich,".$eol.$eol;
		$body .= "i.A. Jonglaria e.V.".$eol;
		return mail($mail_address,$subject,$body,$headers, "-f registration@jonglaria.org");
	}
	// Fixme Encoding of subject is probably wrong, we should do something like
	// https://ncona.com/2011/06/using-utf-8-characters-on-an-e-mail-subject/
	public function sendPaymentMail($id)
	{
		$paycode = $this->createUniqueIdentifierPayment($id);
		$this->tdb->insertPayCode($paycode, $id);
		$eol = "\r\n";
		$mail_address = $this->tdb->getEmail($id);
		$headers = "From: Mexicon <registration@jonglaria.org>".$eol;
		$headers .= "Cc: regcc@jonglaria.org".$eol;
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
		$headers .= "Content-Transfer-Encoding: 8bit".$eol;
		$subject = "Mexicon - Überweisungsinformationen";
		$subject = $this->utf8base64encode($subject);
		$body .= "Hallo ".$this->tdb->getFirstName($id)." ".$this->tdb->getFamilyName($id).",".$eol.$eol;
		$body .= "wir freuen uns über deine Anmeldung an der Mexicon, der 6. Tübinger Jonglierconvention am 15.9. bis 17.9.!".$eol.$eol;
		$body .= "Um deine Anmeldung zu vervollständigen, überweise bitte den Betrag von".$eol.$eol;
		$body .= $this->tdb->getCosts($id)." Euro (Betrag für ".$this->tdb->getAge($id)." Jahre alten Teilnehmer)".$eol.$eol;
		$body .= "innerhalb von spätestens 10 Tagen auf folgendes Konto:".$eol.$eol;
		$body .= "Empfänger: Jonglaria e.V.".$eol;
		$body .= "Konto: DE15 6415 0020 0001 1490 32".$eol;
		$body .= "Betrag: ".$this->tdb->getCosts($id)." EUR".$eol;
		$body .= "Betreff: \"MEXICON ".$paycode."\"".$eol.$eol;
		$body .= "Sobald wir den Eingang des Geldes festgestellt haben, schicken wir dir ein elektronisches Ticket, was du ausdrucken und zur Convention mitbringen kannst.".$eol.$eol;
		$body .= "Wir freuen uns auf dich,".$eol.$eol;
		$body .= "i.A. Jonglaria e.V.".$eol;
		return mail($mail_address,$subject,$body,$headers, "-f registration@jonglaria.org");
	}
	public function sendPaymentMailGala($id)
	{
		$paycode = $this->createUniqueIdentifierPayment($id);
		$this->tdb->insertPayCodeGala($paycode, $id);
		$eol = "\r\n";
		$mail_address = $this->tdb->getEmailGala($id);
		$headers = "From: \"".$this->utf8base64encode("Tübinger Fröschle")."\" <registration@jonglaria.org>".$eol;
		$headers .= "Cc: regcc@jonglaria.org".$eol;
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
		$headers .= "Content-Transfer-Encoding: 8bit".$eol;
		$subject = "Tübinger Fröschle - Überweisungsinformationen";
		$subject = $this->utf8base64encode($subject);
		$body .= "Hallo ".$this->tdb->getFirstNameGala($id)." ".$this->tdb->getFamilyNameGala($id).",".$eol.$eol;
		$body .= "wir freuen uns über deine Anmeldung an dem Adi Pius Kläger Kleinkunstpreis Tübinger Fröschle am 16.9.!".$eol.$eol;
		$body .= "Um deine Anmeldung zu vervollständigen, überweise bitte den Betrag von".$eol.$eol;
		$body .= $this->tdb->getCostsGala($id)." Euro (Betrag für ".$this->tdb->getNumberOfTicketsGala($id)." Ticket(s) mit dem Normalpreis.)".$eol.$eol;
		$body .= "bzw.".$eol.$eol;
		$body .= $this->tdb->getCostsGalaReduced($id)." Euro (Betrag für ".$this->tdb->getNumberOfTicketsGala($id)." reduzierte Ticket(s) (Schüler, Studenten, Rentner, etc.) Bitte selbst korrekt ausrechnen falls sowohl normalpreisige als auch reduzierte Tickets benötigt werden.)".$eol.$eol;
		$body .= "innerhalb von spätestens 10 Tagen auf folgendes Konto:".$eol.$eol;
		$body .= "Empfänger: Jonglaria e.V.".$eol;
		$body .= "Konto: DE15 6415 0020 0001 1490 32".$eol;
		$body .= "Betrag: ".$this->tdb->getCostsGala($id)." EUR (bzw. red. ".$this->tdb->getCostsGalaReduced($id)." EUR, s.o.)".$eol;
		$body .= "Betreff: \"FROESCHLE ".$paycode."\"".$eol.$eol;
		$body .= "Sobald wir den Eingang des Geldes festgestellt haben, schicken wir dir ein elektronisches Ticket, was du ausdrucken und zum Kleinkunstpreis mitbringen kannst.".$eol.$eol;
		$body .= "Wir freuen uns auf dich,".$eol.$eol;
		$body .= "i.A. Jonglaria e.V.".$eol;
		return mail($mail_address,$subject,$body,$headers, "-f registration@jonglaria.org");
	}

	public function createElectronicTicket($id)
	{
		# Todo multiple return values?
		$exec_string = "cd /is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/dynamic && LD_LIBRARY_PATH=$(pwd):\$LD_LIBRARY_PATH ./example1";
		$exec_string .= " -1\"".$this->tdb->getFirstName($id)."\"";	
		$exec_string .= " -2\"".$this->tdb->getFamilyName($id)."\"";
		$exec_string .= " -b\"".$this->tdb->getBirthDate($id)."\"";
		$supervisor = $this->tdb->getSuperVisor($id);
		if($supervisor != "")
		{
			$exec_string .= " -s\"".$supervisor."\"";
		}
		$exec_string .= " -i\"http://jonglaria.org/reg/".$this->createUniqueIdentifier($id)."\"";
		$exec_string .= " | TEMP=. gs";
		$exec_string .= " -sstdout=%stderr";
		$exec_string .= " -dBATCH";
		$exec_string .= " -dNOPAUSE";
		$exec_string .= " -sPAPERSIZE=a4"; 
		$exec_string .= " -dNOEPS";
		$exec_string .= " -dAutoFilterColorImages=false";
		$exec_string .= " -dColorImageFilter=/FlateEncode";
		$exec_string .= " -sDEVICE=pdfwrite";
		$exec_string .= " -o -";
		$exec_string .= " -f -";
		$exec_string .= " -c \"[ /Author (Jonglaria e.V.) /Subject (Electronic Ticket) /Keywords (Convention, Tuebingen, 2017, Juggling, Jonglaria) /DOCINFO pdfmark\"";
		return shell_exec($exec_string);
		/* Debug code: */
		exec($exec_string, $output, $retVal);
		print "<p>RetVal:".$retVal."</p>";
		foreach($output as $out)
		{
			print "<p>out:".$out."</p>";
		}
		return "";
	}
	public function createElectronicTicketGala($id)
	{
		# Todo multiple return values?
		$exec_string = "cd /is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/dynamic && LD_LIBRARY_PATH=$(pwd):\$LD_LIBRARY_PATH ./example1";
		$exec_string .= " -1\"".$this->tdb->getFirstNameGala($id)."\"";	
		$exec_string .= " -2\"".$this->tdb->getFamilyNameGala($id)."\"";
		$exec_string .= " -b\"".$this->tdb->getBirthDateGala($id)."\"";
		$exec_string .= " -i\"http://jonglaria.org/reg/".$this->createUniqueIdentifier($id)."\"";
		$exec_string .= " -g";
		$exec_string .= " | TEMP=. gs";
		$exec_string .= " -sstdout=%stderr";
		$exec_string .= " -dBATCH";
		$exec_string .= " -dNOPAUSE";
		$exec_string .= " -sPAPERSIZE=a4"; 
		$exec_string .= " -dNOEPS";
		$exec_string .= " -dAutoFilterColorImages=false";
		$exec_string .= " -dColorImageFilter=/FlateEncode";
		$exec_string .= " -sDEVICE=pdfwrite";
		$exec_string .= " -o -";
		$exec_string .= " -f -";
		$exec_string .= " -c \"[ /Author (Jonglaria e.V.) /Subject (Electronic Ticket) /Keywords (Convention, Tuebingen, 2017, Juggling, Jonglaria) /DOCINFO pdfmark\"";
		return shell_exec($exec_string);
		/* Debug code: */
		exec($exec_string, $output, $retVal);
		print "<p>RetVal:".$retVal."</p>";
		foreach($output as $out)
		{
			print "<p>out:".$out."</p>";
		}
		return "";
	}

	public function sendElectronicTicket($id)
	{
		// Attachment code from https://stackoverflow.com/questions/12301358/send-attachments-with-php-mail
		$subject = "Elektronisches Ticket Mexicon";
		$subject = $this->utf8base64encode($subject);

		$separator = md5(time());
		$ticket = $this->createElectronicTicket($id);

		$ticketAttachment = chunk_split(base64_encode($ticket));
		$eol = "\r\n";
		$headers = "From: Mexicon <registration@jonglaria.org>".$eol;
		$headers .= "Cc: regcc@jonglaria.org".$eol;
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol;
		$headers .= "Content-Transfer-Encoding: 7bit".$eol;
		$headers .= "This is a MIME encoded message.".$eol;
		// message
		$body = "--" . $separator . $eol;
		$body .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
		$body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
		$body .= "Hallo ".$this->tdb->getFirstName($id)." ".$this->tdb->getFamilyName($id).",".$eol.$eol;
		$body .= "wir freuen uns über deine Anmeldung zur Mexicon, der 6. Tübinger Jonglierconvention am 15.9. bis 17.9.!".$eol.$eol;
		$body .= "Im Anhang findest du ein elektronisches Ticket was du bitte zur Convention ausgedruckt mitbringst.".$eol.$eol;
		$body .= "Wir freuen uns auf dich,".$eol.$eol;
		$body .= "i.A. Jonglaria e.V.".$eol;

		// attachment
		$body .= "--" . $separator . $eol;
		$body .= "Content-Disposition: attachment".$eol;
		$body .= "Content-Length:".strlen($ticket).$eol;
		$body .= "Content-Type: application/octet-stream; name=\"mexicon_ticket_" . $this->createUniqueIdentifier($id) . ".pdf\"" . $eol;
		$body .= "Content-Transfer-Encoding: base64".$eol.$eol;
		$body .= $ticketAttachment.$eol;
		$body .= "--" . $separator . "--";
		# Todo Zusatztext für Minderjährige
		return mail($this->tdb->getEmail($id),$subject,$body,$headers, "-f registration@jonglaria.org");
		// file_put_contents("work.pdf", $ticketAttachment);

	}
	public function sendElectronicTicketGala($id)
	{
		// Attachment code from https://stackoverflow.com/questions/12301358/send-attachments-with-php-mail
		$subject = "Elektronisches Ticket Adi Pius Kläger Kleinkunstpreis Tübinger Fröschle";
		$subject = $this->utf8base64encode($subject);

		$separator = md5(time());
		$ticket = $this->createElectronicTicketGala($id);

		$ticketAttachment = chunk_split(base64_encode($ticket));
		$eol = "\r\n";
		$headers = "From: \"".$this->utf8base64encode("Tübinger Fröschle")."\" <registration@jonglaria.org>".$eol;
		$headers .= "Cc: regcc@jonglaria.org".$eol;
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol;
		$headers .= "Content-Transfer-Encoding: 7bit".$eol;
		$headers .= "This is a MIME encoded message.".$eol;
		// message
		$body = "--" . $separator . $eol;
		$body .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
		$body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
		$body .= "Hallo ".$this->tdb->getFirstNameGala($id)." ".$this->tdb->getFamilyNameGala($id).",".$eol.$eol;
		$body .= "wir freuen uns über deine Anmeldung an dem Adi Pius Kläger Kleinkunstpreis Tübinger Fröschle am 16.9.!".$eol.$eol;
		$body .= "Im Anhang findest du ein elektronisches Ticket was du bitte zum Kleinkunstpreis ausgedruckt mitbringst.".$eol.$eol;
		$body .= "Wir freuen uns auf dich,".$eol.$eol;
		$body .= "i.A. Jonglaria e.V.".$eol;

		// attachment
		$body .= "--" . $separator . $eol;
		$body .= "Content-Disposition: attachment".$eol;
		$body .= "Content-Length:".strlen($ticket).$eol;
		$body .= "Content-Type: application/octet-stream; name=\"mexicon_ticket_" . $this->createUniqueIdentifier($id) . ".pdf\"" . $eol;
		$body .= "Content-Transfer-Encoding: base64".$eol.$eol;
		$body .= $ticketAttachment.$eol;
		$body .= "--" . $separator . "--";
		# Todo Zusatztext für Minderjährige
		return mail($this->tdb->getEmailGala($id),$subject,$body,$headers, "-f registration@jonglaria.org");
		// file_put_contents("work.pdf", $ticketAttachment);

	}

}
?>
