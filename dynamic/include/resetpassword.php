<?php
namespace Jonglaria;

require_once("config.php");
require_once("form.php");
require_once("userdata.php");

class ResetPasswordForm extends Form{
    private $passwd_vstr;
    private $passwd;
    public function validate_passwd($val){
        if($val == ''){
            $this->passwd_vstr = "<b>Passwort darf nicht leer sein:</b> ";
            return false;
        }
        if(mb_strlen($val)<8){
            $this->passwd_vstr = "<b>Passwort muss mindestens 8 Zeichen haben:</b> ";
            return false;
        }
        preg_match('/([a-zA-Z\d]*[^a-zA-Z\d]){2,}[a-zA-Z\d]*/',$val,$matches);
        if(strcmp($matches[0],$val) != 0){
            $this->passwd_vstr = "<b>Passwort muss mindestens zwei Sonderzeichen haben:</b> ";
            return false;
        }
        $this->passwd = $val;
        return true;
    }
    public function validationstring_passwd($val){
        return $this->passwd_vstr;
    }
    public function validate_passwdconfirm($val){
        return !(strcmp($val,$passwd) == 0);
    }
    public function validationstring_passwdconfirm($val){
        return "<b>Passwort ist nicht zu erster Eingabe identisch:</b> ";
    }
    public function checkboxes_button_expectations(){
        $expectations = array();
        $expectations['confirmdata'] = 'checked';
        return $expectations;
    }
    public function validationstring_confirmdata(){
        return "<b>Wir benötigen diese Bestätigung um fortzufahren:</b> ";
    }
}

?>
