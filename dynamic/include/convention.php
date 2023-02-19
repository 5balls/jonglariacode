<?php
namespace Jonglaria;

require_once("config.php");
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
        if(file_exists($this->cfg['userdata_path'].'/'.$val)){
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
            $auth = new Authorization($this->cfg['convention_auth_path'].'/.htusers', $this->cfg['convention_auth_path'].'/.htgroups');
            $auth->addUser($this->username, $this->password);
            $auth->addUserToGroup($this->username, $this->cfg['convention_auth_participants_group']);
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
    public function confirm_order(){
        $username = $this->authuser;
        $data = new UserDataSQL('orderlists', $username);
        $data->fillFromFile();
        $orders = $data->getDataObject('orders');
        if(!is_null($orders)){
            $dc = $orders->dataContent;
            $lindex = count($dc)-1;
            if($dc[$lindex]['status'] === 'open'){
                $dc[$lindex]['status'] = 'confirmed';
                $dc[$lindex]['confirmdate'] = time();
                $neworder = new Data('orders', $GLOBALS['storageduration'], 'Wir benötigen diese Daten um die Bestellung der Tickets für die Convention durchführen zu können', $dc, array('conadminhelpers'));
                $ndata = new UserDataSQL('orderlists', $username);
                $ndata->addDataObjectD($neworder);
                $ndata->storeData();
            }
        }
    }
    public function create_open_order(){
        # Check if there is an order already:
        $username = $this->authuser;
        $data = new UserDataSQL('orderlists', $username);
        $data->fillFromFile();
        if(is_null($data->getDataObject('orders'))){
            # There is no existing order so create a new one with status 'open':
            $orders = array();
            $orders['hashcode'] = substr(md5(strval(time()).$username.$this->cfg['convention_order_salt']),1,8); 
            $orders['status'] = 'open';
            $ordersvariable = array($orders);
            $neworder = new Data('orders', $GLOBALS['storageduration'], 'Wir benötigen diese Daten um die Bestellung der Tickets für die Convention durchführen zu können', $ordersvariable, array('conadminhelpers'));
            $data->addDataObjectD($neworder);
            $data->storeData();
            $GLOBALS['orders'] = $ordersvariable;
            $GLOBALS['openorderhash'] = $orders['hashcode'];
        }
        else{
            # There is one or more orders:
            if(end($data->getDataObject('orders')->dataContent)['status'] === 'open'){
                # Last order is still open, so we can use this:
                $GLOBALS['orders'] = $data->getDataObject('orders')->dataContent;
                $GLOBALS['openorderhash'] = end($data->getDataObject('orders')->dataContent)['hashcode'];
            } 
            else{
                # Last order is at least confirmed, so we need a new one:
                $order = array();
                $order['hashcode'] = substr(md5(strval(time()).$username.$this->cfg['convention_order_salt']),1,8);  
                $order['status'] = 'open';
                $ordersvariable = $data->getDataObject('orders');
                $ordersvariable->dataContent[] = $order;

                $neworder = new Data('orders', $GLOBALS['storageduration'], 'Wir benötigen diese Daten um die Bestellung der Tickets für die Convention durchführen zu können', $ordersvariable->dataContent, array('conadminhelpers'));
                $ndata = new UserDataSQL('orderlists', $username);
                $ndata->addDataObjectD($neworder);
                $ndata->storeData();

                $GLOBALS['orders'] = $ordersvariable->dataContent;
                $GLOBALS['openorderhash'] = $order['hashcode'];
            }
        }
    }
    public function check_precondition(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            switch($_POST['submitdata']){ 
                case 'Person entfernen':
                    $username = $this->authuser;
                    $this->create_open_order();
                    $this->readdata();
                    $tickettorm = $GLOBALS['formcontents']['regperson'][intval($_POST['rmpersonnumber'])-1];
                    if($GLOBALS['openorderhash'] === $tickettorm['orderhash']){
                        unset($GLOBALS['formcontents']['regperson'][intval($_POST['rmpersonnumber'])-1]);
                        var_dump();
                        $neworder = new Data('regperson', $GLOBALS['storageduration'], 'Wir benötigen diese Daten um die Anmeldung der Convention durchführen zu können. Das Alter wird zum Feststellen des Conventionbeitrags benötigt, zum Anderen müssen für Minderjährige zusätzliche Felder ausgefüllt werden damit ein Erziehungsberechtigter / eine vom erziehungsberechtigten beauftragte Betreuungsperson dem Organisationsteam bekannt ist.', $GLOBALS['formcontents']['regperson'], array('conadminhelpers'));

                        $ndata = new UserDataSQL('personaldata', $username);
                        $ndata->addDataObjectD($neworder);
                        $ndata->storeData();
                        header('Location: ' . $this->redirection);
                        die();
                    }
                    break;
                case 'Bestellung abschließen':
                    $this->confirm_order();
                    header('Location: ' . $this->redirection);
                    die();
                    break;
            }
        }
        # If necessary create an open order:
        $this->create_open_order();
        # This is abused to get an array of registered people as well
        $this->readdata();
        foreach($GLOBALS['formcontents']['regperson'] as $ticket_original){
            $ticket = $ticket_original;
            $age = new Age($ticket['birthday']);
            $ticket['ageconvention'] = strval($age->ageConvention);
            $ticket['costs'] = strval(calculateConventionPrice($age->ageConvention, true));
            $GLOBALS['tickets'][] = $ticket;
        }
        unset($GLOBALS['formcontents']);
        return true;
    }
    public function beforestorage(){
        $datafrompost = $this->sanitized_formdata;
        error_log('datafrompost:');
        error_log(var_export($datafrompost, true));
        error_log('storedata:');
        error_log(var_export($this->storedata, true));
        $this->readdata();
        if(empty($GLOBALS['formcontents'])){
            $datafrompost['regperson'] = array($datafrompost['regperson']);
            $this->sanitized_formdata = $datafrompost;
        }
        else{
            $datafromfile = $GLOBALS['formcontents'];
            $datafromfile['regperson'][] = $datafrompost['regperson'];
            $this->sanitized_formdata = $datafromfile;
        }
        unset($GLOBALS['formcontents']);
        return true;
    }
    public function validationstring_regperson($vals){
        return $this->vs_regperson;
    }
    public function validate_email($val){
        return filter_var($val, FILTER_VALIDATE_EMAIL);
    }
    public function validationstring_email($val){
        return "Emailadresse ungültig: ";
    }
}

/*class OrderConfirm extends Form{
    private $vs_regperson = array();
    public function validate_regperson($vals){
        return $retarray;
    }
    public function confirm_payment(){
        $username = $this->authuser;
        $data = new UserDataSQL('orderlists', $username);
        $data->fillFromFile();
        $orders = $data->getDataObject('orders');
        if(!is_null($orders)){
            $dc = $orders->dataContent;
            $lindex = count($dc)-1;
            if($dc[$lindex]['status'] === 'confirmed'){
                $dc[$lindex]['status'] = 'payed';
                $dc[$lindex]['paymentconfirmdate'] = time();
                $neworder = new Data('orders', $GLOBALS['storageduration'], 'Wir benötigen diese Daten um die Bestellung der Tickets für die Convention durchführen zu können', $dc, array('conadminhelpers'));
                $ndata = new UserDataSQL('orderlists', $username);
                $ndata->addDataObjectD($neworder);
                $ndata->storeData();
            }
        }
    }
    
    public function check_precondition(){
       return true;
    }
    public function beforestorage(){
    }
    public function validationstring_regperson($vals){
        return $this->vs_regperson;
    }
    public function validate_email($val){
        return filter_var($val, FILTER_VALIDATE_EMAIL);
    }
    public function validationstring_email($val){
        return "Emailadresse ungültig: ";
    }
}
*/

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
            else{
                $collectsupervisorinfo = new CollectedUserData($this->cfg['userdata_path', 'supervisor.json');
                foreach($collectsupervisorinfo->userData as $child => $childvals){
                    if(strcmp($childvals->getDataObject('supervisoraccountname')->dataContent, $_SERVER['PHP_AUTH_USER']) == 0){
                        $childdata = new UserDataCU('personaldata',$child);
                        $childsupervisordata = new UserDataCU('supervisor',$child);
                        $GLOBALS['supervisorrequest'][] = array('accountname' => $child, 'prename' => $childdata['regperson']['prename'], 'surname' => $childdata['regperson']['surname'], 'responsible' => $childsupervisordata['responsible']);
                    }
                }
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

        if(!file_exists($this->cfg['userdata_path'].'/'.$val)){
            $this->supervisorerror = "\"".$val."\" ist noch nicht vergeben, bitte nochmal Schreibweise überprüfen: ";
            return false;
        }
        return true;
    }
    public function validationstring_supervisoraccountname($val){
        return $this->supervisorerror;
    }

}

class Payment extends Form{
    private $preregperiod = True;
    private $age;
    public function check_precondition(){
        $this->age = new Age();
        if(!$this->age->couldreaddata){
            $GLOBALS['personaldatavalid'] = False;
            return False;
        }
        $GLOBALS['costs'] = calculateConventionPrice($this->age->ageConvention, $this->preregperiod);
        $personaldata = new UserDataC('personaldata');
        if(!isset($personaldata['regperson'])){
            $GLOBALS['nopersonaldata'] = True;
            return false;
        }
        $GLOBALS['regperson'] = $personaldata['regperson'];
        $GLOBALS['email'] = $personaldata['email'];
        $GLOBALS['age'] = $this->age->ageConvention;
        if($this->age->ageConvention < 18){
            $GLOBALS['minor'] = True;
	    $supervisor = new UserDataC('supervisor');
            if(!$supervisor->nofile){
                if(!isset($supervisor['supervisoraccountname'])){
                    $GLOBALS['nosupervisor'] = True;
                    return false;
                }
            }
	    else
            {
                $GLOBALS['nosupervisor'] = True;
                return false;
            }
            $GLOBALS['supervisoraccountname'] = $supervisor['supervisoraccountname'];
        }
        return true;
    }
}

class InfoUserData{
    public $userData = array();
    public function __construct($storeindb = null){
        $username = $_SERVER['PHP_AUTH_USER'];
        if(is_null($storeindb))
        {
            $base_path = $this->cfg['userdata_path'];
            $userfiles = explode("\n",shell_exec("ls ".$base_path."/".$username."/*.json"));
            foreach($userfiles as $userfile){
                $filenamewithoutpath = end(explode('/', $userfile));
                $userdata = new UserData($userfile, $username);
                $userdata->fillFromFile();
                $this->userData[$filenamewithoutpath] = $userdata;
            }
        }
        else
        {
            $userdatasql = new UserDataSQL("", $username);
            $usertables = $userdatasql->getTableList();
            foreach($usertables as $usertable){
                $userdata = new UserDataSQL($usertable, $username);
                $userdata->fillFromFile();
                $this->userData[$usertable] = $userdata;
            }
        }
    }
}

?>
