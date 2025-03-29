<?php
namespace Jonglaria;

require_once("config.php");
require_once("form.php");
require_once("userdata.php");

class RequestAccountForm extends Form{
    private $vs_regperson = array();
    public function validate_regperson($vals){
        $retarray = array();
        foreach($vals as $key=>$val){
            $retarray[$key] = true;
            if($val == ''){
                $retarray[$key] = false;
                $this->vs_regperson[$key] = "Feld muss ausgefüllt werden: ";
            }
            if(strcmp($key,'email') == 0){
                $retarray[$key] = filter_var($val, FILTER_VALIDATE_EMAIL);
                if(!$retarray[$key]){
                    $this->vs_regperson[$key] = "Email nicht gültig: ";
                }
                continue;
            }
        }
        return $retarray;
    }
    public function validate_resetdata($val){
        return true;
    }
    public function validationstring_regperson($val){
        return $this->vs_regperson;
    }
    public function checkboxes_button_expectations(){
        $expectations = array();
        $expectations['confirmdata'] = 'checked';
        $expectations['resetdata'] = 'dontcare';
        $group = array();
        $group['juggler'] = 'checked';
        $group['conparticipant'] = 'dontcare';
        $group['conorga'] = 'dontcare';
        $group['jonglariamember'] = 'dontcare';
        $group['jonglariadirector'] = 'dontcare';
        $expectations['group'] = $group;
        return $expectations;
    }
    public function validationstring_confirmdata(){
        return "Wir benötigen diese Bestätigung um fortzufahren: ";
    }
    public function validationstring_group(){
        $retstrings = array();
        $retstrings['juggler'] = 'Jeder sollte der Gruppe Jongleur angehören: ';
        return $retstrings;
    }
}

?>
