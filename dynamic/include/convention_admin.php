<?php
namespace Jonglaria;

require_once("form.php");
require_once("userdata.php");
require_once("auth.php");
require_once("conventionhelpers.php");

class OrderConfirm extends Form{
    private $vs_regperson = array();
    public function validate_username($vals){
        return true;
    }
    public function validationstring_username($val){
        return "Benutzername ungültig: ";
    }
    public function validate_orderhash($vals){
        return true;
    }
    public function validationstring_orderhash($val){
        return "Orderhash ungültig: ";
    } 
    public function confirm_payment($username, $hashcode){
        $data = new UserDataSQL('orderlists', $username);
        $data->fillFromFile();
        $orders = $data->getDataObject('orders');
        if(!is_null($orders)){
            $dc = $orders->dataContent;
            for($lindex=0;$lindex<count($dc);$lindex++){
                if($dc[$lindex]['hashcode'] === $hashcode){
                    if($dc[$lindex]['status'] === 'confirmed'){
                        $dc[$lindex]['status'] = 'paymentconfirmed';
                        $dc[$lindex]['paymentconfirmdate'] = time();
                        $neworder = new Data('orders', $GLOBALS['storageduration'], 'Wir benötigen diese Daten um die Bestellung der Tickets für die Convention durchführen zu können', $dc, array('conadminhelpers'));
                        $ndata = new UserDataSQL('orderlists', $username);
                        $ndata->addDataObjectD($neworder);
                        $ndata->storeData();
                    }
                }
            }
        }
    }

    public function check_precondition(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            switch($_POST['submitdata']){ 
               case 'Zahlung ist erfolgt':
                   $this->confirm_payment($_POST['username'], $_POST['hashcode']);
                   header('Location: ' . $this->redirection);
                   die();
                   break;
            }
        }
	$orders = new CollectedUserDataSQL("","orderlists");
        foreach($orders->userData as $user=>$userdata){
            $gorders[$user] = $userdata->getDataObject("orders")->dataContent;
        }

        $GLOBALS["orders"] = $gorders;

        $pd = new CollectedUserDataSQL("","personaldata");
        foreach($pd->userData as $user=>$userdata){
            $gpd[$user] = $userdata->getDataObject("regperson")->dataContent;
        }

        $GLOBALS["personaldata"] = $gpd;
        return true;
    }
    public function beforestorage(){
    }
}

class StatisticsOrders extends Form{
    private $vs_regperson = array();
    public function check_precondition(){
	$orders = new CollectedUserDataSQL("","orderlists");
        foreach($orders->userData as $user=>$userdata){
            $gorders[$user] = $userdata->getDataObject("orders")->dataContent;
        }

        $GLOBALS["orders"] = $gorders;

        $pd = new CollectedUserDataSQL("","personaldata");
        foreach($pd->userData as $user=>$userdata){
            $gpd[$user] = $userdata->getDataObject("regperson")->dataContent;
        }

        $GLOBALS["personaldata"] = $gpd;
        return true;
    }
    public function beforestorage(){
    }
}

?>
