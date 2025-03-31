<?php
namespace Jonglaria;

require_once("config.php");
require_once("form.php");
require_once("userdata.php");
require_once("config.php");

class RequestAccountForm extends Form{
    private $vs_regperson = array();
    public function validate_regperson($vals){
        $retarray = array();
        foreach($vals as $key=>$val){
            $retarray[$key] = true;
            if($val == ''){
                $retarray[$key] = false;
                $this->vs_regperson[$key] = "<b>Feld muss ausgefüllt werden:</b> ";
            }
            if(strcmp($key,'email') == 0){
                $retarray[$key] = filter_var($val, FILTER_VALIDATE_EMAIL);
                if(!$retarray[$key]){
                    $this->vs_regperson[$key] = "<b>Email nicht gültig:</b> ";
                }
                continue;
            }
            if(strcmp($key,'loginname') == 0){
                // Check that loginname is not empty (should be filled
                // by code if not by user):
                if($val == ''){
                    $retarray[$key] = false;
                    $this->vs_regperson[$key] = "<b>Interner Serverfehler - bitte Admin Florian Pesth (fpesth@gmx.de) verständigen!:</b> ";
                    continue;
                }
                // Check if user in LDAP:
                $cfg = new Config();
                $ldap_conn = ldap_connect($cfg['ldap_server']);
                if(!$ldap_conn){
                    $retarray[$key] = false;
                    $this->vs_regperson[$key] = "<b>Interner Serverfehler - bitte Admin Florian Pesth (fpesth@gmx.de) verständigen!:</b> ";
                    continue;
                }
                ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
                $ldap_bind = ldap_bind($ldap_conn, $cfg['ldap_binduser'],$cfg['ldap_bindpasswd']);
                if(!$ldap_bind){
                    $retarray[$key] = false;
                    $this->vs_regperson[$key] = "<b>Interner Serverfehler - bitte Admin Florian Pesth (fpesth@gmx.de) verständigen!:</b> ";
                    continue;
                }
                $ldap_filter = "(objectClass=inetOrgPerson)";
                $ldap_search_base = "ou=Users,dc=jonglaria,dc=org";
                $ldap_search = ldap_search($ldap_conn, $ldap_search_base, $ldap_filter);
                $entry = ldap_first_entry($ldap_conn, $ldap_search);

                $isused = false;
                do {
                    $dn = ldap_get_dn($ldap_conn, $entry);
                    $cn = ldap_get_values($ldap_conn, $entry, 'cn');
                    $isused = (strcmp($cn[0],$val) == 0);
                    if($isused) break;
                } while ($entry = ldap_next_entry($ldap_conn, $entry));
                if($isused){
                    $retarray[$key] = false;
                    $this->vs_regperson[$key] = "<b>Loginname leider schon vergeben:</b> ";
                    continue;
                }
                $retarray[$key] = true;
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
