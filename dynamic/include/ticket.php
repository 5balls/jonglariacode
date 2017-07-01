<?php
# Class for sending emails
namespace Jonglaria;

require_once("ticketdb.php");
class Ticket
{
	private $salt = "ksajdfz26kadfls8";
	private	$tdb = "";
	public function __construct()
	{
		$this->tdb = new TicketDatabase();
	}

	public function createUniqueIdentifier($id)
	{
		return substr(md5($id.$this->salt),1,8);
	}

	public function sendConfirmationMail($id, $subject, $body_before_url, $body_after_url, $url_base)
	{
		$mail_address = getMailAddress($id);
		$body = $body_before_url;
		$body .= $url_base;
		$body .= "?mail=".urlencode(trim($mail_adress));
		$body .= "&id=".urlencode(md5($id.$salt));
		$body .= $body_after_url;
		return mail($mailadress, $subject, $body);
	}

	public function createElectronicTicket($id)
	{
		# Todo multiple return values?
		$exec_string = "LD_LIBRARY_PATH=$(pwd):\$LD_LIBRARY_PATH ./example1";
		$exec_string .= " -1\"".$this->tdb->getFirstName($id)."\"";	
		$exec_string .= " -2\"".$this->tdb->getFamilyName($id)."\"";
		$exec_string .= " -b\"".$this->tdb->getBirthDate($id)."\"";
		$supervisor = $this->tdb->getSuperVisor($id);
		if($supervisor != "")
		{
			$exec_string .= " -s\"".$supervisor."\"";
		}
		$exec_string .= " -i\"http://jonglaria.org/reg/".$this->createUniqueIdentifier($id)."\"";
		$exec_string .= " | gs";
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
		if($retval == 0)
		{
			return $pdf_output;
		}
		else
		{
			return "";
		}
	}
	public function sendElectronicTicket($id)
	{
		// Attachment code from https://stackoverflow.com/questions/12301358/send-attachments-with-php-mail
		$subject = "Elektronisches Ticket Mexicon";

		$separator = md5(time());
		
		$ticketAttachment = chunk_split(base64_encode($this->createElectronicTicket($id)));
		$eol = "\r\n";
		$headers = "From: Mexicon <registration@jonglaria.org>".$eol;
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol;
		$headers .= "Content-Transfer-Encoding: 7bit".$eol;
		$headers .= "This is a MIME encoded message.".$eol;
		// message
		$body = "--" . $separator . $eol;
		$body .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
		$body .= "Content-Transfer-Encoding: 8bit".$eol;
		$body .= "Hallo ".$this->tdb->getFirstName($id)." ".$this->tdb->getFamilyName($id).",".$eol.$eol;
		$body .= "wir freuen uns über deine Anmeldung zur Mexicon, der 6. Tübinger Jonglierconvention am 15.9. bis 17.9.!".$eol.$eol;
		$body .= "Im Anhang findest du ein elektronisches Ticket was du bitte zur Convention ausgedruckt mitbringst.".$eol.$eol;
		$body .= "Wir freuen uns auf dich,".$eol.$eol;
		$body .= "i.A. Jonglaria e.V.";

		// attachment
		$body .= "--" . $separator . $eol;
		$body .= "Content-Type: application/octet-stream; name=\"mexicon_ticket_" . $this->createUniqueIdentifier($id) . ".pdf\"" . $eol;
		$body .= "Content-Transfer-Encoding: base64" . $eol;
		$body .= "Content-Disposition: attachment" . $eol;
		$body .= $ticketAttachment . $eol;
		$body .= "--" . $separator . "--";
		# Todo Zusatztext für Minderjährige
		return mail($this->tdb->getEmail($id),$subject,$body,$headers, "-f registration@jonglaria.org");
		// file_put_contents("work.pdf", $ticketAttachment);

	}
}
?>
