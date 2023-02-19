<?php
namespace Jonglaria;

require_once("form.php");
require_once("userdata.php");
require_once("auth.php");
require_once("conventionhelpers.php");

class PaymentConfirmation extends Form{
    private $username;
    private $filename_personaldata;
    private $role;
    private $supervisorerror;
    public function check_precondition(){
        $collectpaymentdata = new CollectedUserData($this->cfg['userdata_path'], 'payment.json');
        $personaldata = new CollectedUserData($this->cfg['userdata_path'], 'personaldata.json');
        $authdata = new CollectedUserData($this->cfg['userdata_path'], 'authdata.json');
        $counter = 0;
        foreach($collectpaymentdata->userData as $child => $childvals){
            $counter = $counter + 1;
            $payinfo = $childvals->getDataObject('paymentinfo')->dataContent;
            echo "<p>".strval($counter).". ".strval($child)." ".strval($payinfo['costs'])."Euro Code:".strval($payinfo['paymentcode'])."</p>";
        }
        $counter2 = 0;
        foreach($personaldata->userData as $child => $childvals){
            $counter2 = $counter2 + 1;
            $regperson = $childvals->getDataObject('regperson')->dataContent;
            echo "<p>".strval($counter2).". ".strval($child)." ".strval($regperson['prename'])." ".strval($regperson['surname'])."</p>";
        }
        $counter3 = 0;
        foreach($authdata->userData as $child => $childvals){
            $counter3 = $counter3 + 1;
            $authperson = $childvals->getDataObject('accountname')->dataContent;
            echo "<p>".strval($counter3).". ".strval($authperson)."</p>";
        }

        return true;
    }

}

?>
