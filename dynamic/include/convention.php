<?php
namespace Jonglaria;

require_once("form.php");
require_once("userdata.php");
require_once("auth.php");
require_once("conventionhelpers.php");

class AuthData extends Form{
    private $vs_accountname;
    private $username;
    private $password;
    public function validate_email($val){
        return filter_var($val, FILTER_VALIDATE_EMAIL);
    }
    public function validationstring_email($val){
        return "Emailadresse ungültig: ";
    }
    public function validate_password($val){
        $this->password = $val;
        return !($val == '');
    }
    public function validationstring_password($val){
        return "Passwort darf nicht leer sein: ";
    }
    public function checkboxes_checked(){
        return array('confirmdata');
    }
    public function validationstring_confirmdata(){
        return "Wir benötigen diese Bestätigung um fortzufahren: ";
    }
    public function validate_accountname($val){
        $this->username = $val;
        if(!(preg_match("@^[a-zA-Z0-9_]+$@",$val) === 1)){
            $this->vs_accountname = "Accountname ungültig (nur die Zeichen a-z, A-Z, 0-9 und _ sind erlaubt): ";
            return false;
        }
        if(file_exists('/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/userdata/'.$val)){
            $this->vs_accountname = "Accountname ist schon belegt, bitte anderen wählen!: ";
            return false;
        }
        return true;
    }
    public function validationstring_accountname($vals){
        return $this->vs_accountname;
    }
    public function beforestorage(){
        try{
            $auth = new Authorization('/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/convention/access/.htusers', '/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/convention/access/.htgroups');
            $auth->addUser($this->username, $this->password);
            $auth->addUserToGroup($this->username, 'con18_reg');
        } catch(Exception $exception){
            $GLOBALS['exception'] = $exception;
            return false;
        }
        return true;
    }

}

class PersonalData extends Form{
    private $vs_regperson = array();
    public function validate_regperson($vals){
        $retarray = array();
        foreach($vals as $key=>$val){
            $retarray[$key] = true;
            if($val == ''){
                $retarray[$key] = false;
                $this->vs_regperson[$key] = "Feld muss ausgefüllt werden: ";
            }
	    if(strcmp($key,'birthday')==0){
		$birthdaytimestamp = strtotime($val);
		if($birthdaytimestamp > time()){
		    $retarray[$key] = false;
		    $this->vs_regperson[$key] = "Datum liegt in Zukunft: ";
		}
	    }
        }

        return $retarray;
    }
    public function validationstring_regperson($vals){
        return $this->vs_regperson;
    }
}

class SupervisorData extends Form{
    private $username;
    private $filename_personaldata;
    private $role;
    private $supervisorerror;
    public function check_precondition(){
        $age = new Age();
        if($age->couldreaddata){
            if($age->ageConvention < 18){
                $GLOBALS['minor'] = true;
                $this->role = 'minor';
            }
        }
        else{
            $GLOBALS['datanotentered'] = true;
            return false;
        }
        return true;
    }
    public function validate_supervisoraccountname($val){
        if(strcmp($val, $_SERVER['PHP_AUTH_USER'])==0){
            $this->supervisorerror = "Das ist dein eigener Accountname. Hier den Accountnamen der Betreuungsperson eingeben (diese fragen!): ";
            return false;
        }

        if(!file_exists('/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/userdata/'.$val)){
            $this->supervisorerror = "\"".$val."\" ist noch nicht vergeben, bitte nochmal Schreibweise überprüfen: ";
            return false;
        }
        return true;
    }
    public function validationstring_supervisoraccountname($val){
        return $this->supervisorerror;
    }

}



?>
