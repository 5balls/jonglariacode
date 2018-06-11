<?php
namespace Jonglaria;

include "form.php";
include "userdata.php";

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

    public function validate_birthdate($val){
    }
}



?>
