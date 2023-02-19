<?php
namespace Jonglaria;

require_once("config.php");
require_once("form.php");
require_once("userdata.php");
require_once("auth.php");
require_once("conventionhelpers.php");

class ShowData extends Form{
    private $vs_ticket = array();
    public function validate_prename($val){
        if(!(preg_match("@^[a-zA-Z \-]+$@",$val) === 1)){
            return false;
        }
        else{
            return true;
        }
    }
    public function validationstring_prename($vals){
        return "Vorname darf keine Umlaute und Sonderzeichen enthalten:";
    }
    public function validate_surname($val){
        if(!(preg_match("@^[a-zA-Z \-]+$@",$val) === 1)){
            return false;
        }
        else{
            return true;
        }
    }
    public function validationstring_surname($vals){
        return "Nachname darf keine Umlaute und Sonderzeichen enthalten:";
    }
    public function validate_email($val){
        return filter_var($val, FILTER_VALIDATE_EMAIL);
    }
    public function validationstring_email($val){
        return "Emailadresse ungültig: ";
    }
    public function validate_ticket($vals){
        $retarray = array();
        $emptarray = array();
        $maxres = array();
        foreach($vals as $key=>$val){
            $retarray[$key] = true;
	    $emptarray[$key] = false;
            if($val == ''){
                $emptarray[$key] = true;
            }
            $maxres[$key] = false;
	    if(intval($val) > 15){
                $maxres[$key] = true;
                $retarray[$key] = false;
                $this->vs_ticket[$key] = "Maximale Anzahl Tickets bei einer Reservierung ist 15 (Bei Bedarf bitte zweite Reservierung durchführen):";
	    }
        }
        $allempty = true;
        foreach($emptarray as $key=>$empt){
            if($empt == false){
                $allempty = false;
            }
        }
        if ($allempty == true){
            foreach($vals as $key=>$val){
                $retarray[$key] = false;
                $this->vs_ticket[$key] = "Bitte ein Feld für Ticketanzahl ausfüllen:";
            }
        }
        return $retarray;
    }
    public function validationstring_ticket($vals){
        return $this->vs_ticket;
    }
    public function checkboxes_checked(){
        return array('confirmdata');
    }
    public function validationstring_confirmdata(){
        return "Wir benötigen diese Bestätigung um fortzufahren: ";
    }
    public function appendCSVLineHelper($fileHandler, $fileName, $args){
        $retval = fwrite($fileHandler, $args[0]);
        if($retval != FALSE){
            return True;
        }
        else{
            return False;
        }
    }
    public function beforestorage(){
        $atomicHandler = new AtomicFile();
        $base_path = $this->cfg['show_path'].'/';
        $filename = $base_path . 'reservations.csv';
        $csvline = '"'.$_POST['prename'].'";"'.$_POST['surname'].'";"'.$_POST['email'].'";'.$_POST['ticket']['adult'].';'.$_POST['ticket']['reduced']."\n";
        $retval = $atomicHandler->atomicAppendAction($filename, $this, "appendCSVLineHelper", array($csvline));
        return $this->sendPaymentMail();
    }
    public function utf8base64encode($string)
    {
        return "=?utf-8?B?".base64_encode($string)."?=";
    }
    public function sendPaymentMail()
    {
        $eol = "\r\n";
        $mail_address = $_POST['email'];
        $headers = "From: ".$this->cfg['show_email_from'].$eol;
	# TODO maybe add CC to header if needed
        $headers .= "MIME-Version: 1.0".$eol;
        $headers .= "Content-Type: text/plain; charset=\"utf-8\"".$eol;
        $headers .= "Content-Transfer-Encoding: 8bit".$eol;
        $subject = $this->cfg['show_email_subject'];
        $subject = $this->utf8base64encode($subject);
        $body .= $this->cfg['show_email_body_thanks'].$eol.$eol;
        if($_POST['ticket']['adult'] != ''){
            $ticket_adult = $_POST['ticket']['adult'];
        }
        else{
            $ticket_adult = "0";
        }
        if($_POST['ticket']['reduced'] != ''){
            $ticket_reduced = $_POST['ticket']['reduced'];
        }
        else{
            $ticket_reduced = "0";
        }

        $body .= "Wir haben für Sie ";
	if($ticket_adult < 2){
		$body .= $ticket_adult." Ticket";
	}
	else{
		$body .= $ticket_adult." Tickets";
	}
	$body .= " für Erwachsene";
	if($ticket_reduced > 0){
		$body .= " und ";
		if($ticket_reduced < 2){
			$body .= $ticket_reduced." ermäßigtes Ticket";
		}
		else{
			$body .= $ticket_reduced." ermäßigte Tickets";
		}
	}
	$body .= " reserviert.".$eol.$eol;
        $body .= "Bitte holen Sie die ";
	if($ticket_reduced + $ticket_adult < 2){
		$body .= "reservierte Karte";
	}
	else{
		$body .= "reservierten Karten";
	}
	$body .= " am Veranstaltungsabend bis spätestens 19:30 Uhr an der Abendkasse im Foyer des Festsaals ab.".$eol;
        $body .= "Die Bezahlung erfolgt vor Ort in Bar.".$eol.$eol;

        $body .= "Herzliche Grüße".$eol.$eol;

        $body .= "Ihr Galashow-Organisationsteam".$eol.$eol;

        $body .= "www.jonglaria.org".$eol.$eol;
        return mail($mail_address,$subject,$body,$headers, "-f registration@jonglaria.org");
    }
}

