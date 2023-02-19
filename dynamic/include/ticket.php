<?php
# Class for sending emails
namespace Jonglaria;

require_once("config.php");
require_once("ticketdb.php");
require_once("convention.php");
class Ticket
{
	private $salt;
	private $conf_salt; 
	private $pay_salt;
	private	$tdb = "";
        private $authuser;
        public $personaldata;
        public $costs;
        public $age;
        public $paycode;
        protected $cfg;
	public function __construct($authuser = null)
        {
            $this->cfg = new Config();
            $this->salt = $this->cfg['tickets_salt'];
            $this->conf_salt = $this->cfg['tickets_config_salt'];
            $this->pay_salt = $this->cfg['tickets_payment_salt'];
            $this->personaldata = new UserDataC('personaldata');
            $this->age = new Age();
            if(null == $authuser){
                $authuser = $_SERVER['PHP_AUTH_USER'];
            }
            $this->authuser = $authuser;
            if(!$this->age->couldreaddata){
                $GLOBALS['personaldatavalid'] = False;
                return False;
            }
            $this->costs = calculateConventionPrice($this->age->ageConvention, True);
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
	public function sendConfirmationMail()
	{
		$eol = "\r\n";
		$mail_address = $this->personaldata['email'];
		$regcode = $this->createUniqueIdentifierConfirmation(trim($mail_address));
		$headers = "From: ".$this->cfg['convention_email_from'].$eol;
                # TODO Possible CC for email
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
		$headers .= "Content-Transfer-Encoding: 8bit".$eol;
		$subject = "Cookiecon - Bestätigung Emailadresse";
		$subject = $this->utf8base64encode($subject);
		$body .= "Hallo ".$this->personaldata['regperson']['prename']." ".$this->personaldata['regperson']['surname'].",".$eol.$eol;
		$body .= "wir freuen uns über dein Interesse an der ".$this->cfg['convention_name_with_dates']."!".$eol.$eol;
		$body .= "Emailversand an deine Adresse funktioniert anscheinend!".$eol.$eol;
		$body .= "Wir freuen uns auf dich,".$eol.$eol;
		$body .= "i.A. ".$this->cfg['club_name'].$eol;
		return mail($mail_address,$subject,$body,$headers, "-f registration@jonglaria.org");
	}
	public function sendConfirmationMailGala($id)
	{
#todo not needed this year
	}
	// Fixme Encoding of subject is probably wrong, we should do something like
	// https://ncona.com/2011/06/using-utf-8-characters-on-an-e-mail-subject/
	public function sendPaymentMail()
        {
            $id = $this->authuser;
            $paycode = $this->createUniqueIdentifierPayment($id);
            $this->paycode = $paycode;
            $eol = "\r\n";
            $mail_address = $this->personaldata['email'];
            $headers = "From: ".$this->cfg['convention_email_from'].$eol;
            # TODO add CC?
            $headers .= "MIME-Version: 1.0".$eol;
            $headers .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
            $headers .= "Content-Transfer-Encoding: 8bit".$eol;
            $subject = "Cookiecon - Überweisungsinformationen";
            $subject = $this->utf8base64encode($subject);
            $body .= "Hallo ".$this->personaldata['regperson']['prename']." ".$this->personaldata['regperson']['surname'].",".$eol.$eol;
            $body .= "wir freuen uns über deine Anmeldung an der ".$this->cfg['convention_name_with_dates']."!".$eol.$eol;
            $body .= "Um deine Anmeldung zu vervollständigen, überweise bitte den Betrag von".$eol.$eol;
            $body .= $this->costs." Euro (Betrag für ".$this->age->ageConvention." Jahre alten Teilnehmer)".$eol.$eol;
            $body .= "innerhalb von spätestens 10 Tagen auf folgendes Konto:".$eol.$eol;
            $body .= "Empfänger: ".$this->cfg['club_name'].$eol;
            $body .= "Konto: ".$this->cfg['iban'].$eol;
            $body .= "Betrag: ".$this->costs." EUR".$eol;
            $body .= "Betreff: \"COOKIECON ".$this->paycode."\"".$eol.$eol;
            $body .= "Sobald wir den Eingang des Geldes festgestellt haben, schicken wir dir ein elektronisches Ticket, was du ausdrucken und zur Convention mitbringen kannst.".$eol.$eol;
            $body .= "Wir freuen uns auf dich,".$eol.$eol;
            $body .= "i.A. ".$this->cfg['club_name'].$eol;
            return mail($mail_address,$subject,$body,$headers, "-f registration@jonglaria.org");
        }
	public function sendPaymentMailGala($id)
	{
#todo not needed this year
	}

	public function createElectronicTicket()
        {
            $id = $this->authuser;
            print "<p>".$id."</p>";
            $personaldata = new UserDataCU('personaldata', $id);

# Todo multiple return values?
            # #TODO this needs to be redone, maybe with docutils
            # Here an executable was called - we should do this more elegantly
/*
            $exec_string .= " -1\"".$personaldata['regperson']['prename']."\"";	
            $exec_string .= " -2\"".$personaldata['regperson']['surname']."\"";
            $exec_string .= " -b\"".$personaldata['regperson']['birthday']."\"";
            $supervisor = new UserDataCU('supervisor', $id);
            if($supervisor['supervisoraccountname'] != Null)
            {
                $svpersonaldata = new UserDataCU('personaldata', $supervisor['supervisoraccountname']);
                $exec_string .= " -s\"".$svpersonaldata['regperson']['prename']." ".$svpersonaldata['regperson']['surname']."\"";
            }
            $exec_string .= " -i\"http://jonglaria.org/reg/".$this->createUniqueIdentifier($id)."\""; */
            /*$exec_string .= " | TEMP=. gs";
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
            $exec_string .= " -c \"[ /Author (Jonglaria e.V.) /Subject (Electronic Ticket) /Keywords (Convention, Tuebingen, 2018, Juggling, Jonglaria, Cookiecon) /DOCINFO pdfmark\"";*/
            print "<p>".$exec_string."</p>";
            #return shell_exec($exec_string);
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
#todo not needed
	}
        public function createElectronicTicketFile($filename)
        {
            $electronic_ticket = $this->createElectronicTicket();
            file_put_contents($filename, $electronic_ticket);
        }

	public function sendElectronicTicket($ticket_filename)
        {
            $id = $this->authuser;
            $personaldata = new UserDataCU('personaldata', $id);
            // Attachment code from https://stackoverflow.com/questions/12301358/send-attachments-with-php-mail
            $subject = "Elektronisches Ticket Cookiecon";
            $subject = $this->utf8base64encode($subject);

            $separator = md5(time());
            $ticket = file_get_contents($ticket_filename);

            $ticketAttachment = chunk_split(base64_encode($ticket));
            $eol = "\r\n";
            $headers = "From: ".$this->cfg['convention_email_from'].$eol;
            # TODO Add CC?
            $headers .= "MIME-Version: 1.0".$eol;
            $headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol;
            $headers .= "Content-Transfer-Encoding: 7bit".$eol;
            $headers .= "This is a MIME encoded message.".$eol;
            // message
            $body = "--" . $separator . $eol;
            $body .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
            $body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
            $body .= "Hallo ".$personaldata['regperson']['prename']." ".$personaldata['regperson']['surname'].",".$eol.$eol;
            $body .= "wir freuen uns über deine Anmeldung zur ".$this->cfg['convention_name_with_dates']."!".$eol.$eol;
            $body .= "Im Anhang findest du ein elektronisches Ticket was du bitte zur Convention ausgedruckt mitbringst.".$eol.$eol;
            $body .= "Wir freuen uns auf dich,".$eol.$eol;
            $body .= "i.A. ".$this->cfg['club_name'].$eol;

            // attachment
            $body .= "--" . $separator . $eol;
            $body .= "Content-Disposition: attachment".$eol;
            $body .= "Content-Length:".strlen($ticket).$eol;
            $body .= "Content-Type: application/octet-stream; name=\"convention_ticket_" . $this->createUniqueIdentifier($id) . ".pdf\"" . $eol;
            $body .= "Content-Transfer-Encoding: base64".$eol.$eol;
            $body .= $ticketAttachment.$eol;
            $body .= "--" . $separator . "--";
# Todo Zusatztext für Minderjährige
            return mail($personaldata['regperson']['email'],$subject,$body,$headers, "-f registration@jonglaria.org");
            // file_put_contents("work.pdf", $ticketAttachment);

        }
	public function sendElectronicTicketGala($id)
	{
#todo not needed this year
	}

}
?>
