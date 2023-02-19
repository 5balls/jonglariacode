<?php
namespace Jonglaria;

require_once("form.php");
require_once("userdata.php");
require_once("auth.php");
require_once("conventionhelpers.php");

$orders = new CollectedUserDataSQL("","orderlists");
foreach($orders->userData as $user=>$userdata){
    $gorders[$user] = $userdata->getDataObject("orders")->dataContent;
}

$pd = new CollectedUserDataSQL("","personaldata");
foreach($pd->userData as $user=>$userdata){
    $gpd[$user] = $userdata->getDataObject("regperson")->dataContent;
}

$plotdata_cdate = array();
$plotdata_count = array();

$currentline = 0;
foreach($gorders as $user=>$order){
    foreach($order as $sorder){
        if((strcmp($sorder["status"],"confirmed") == 0) or (strcmp($sorder["status"],"paymentconfirmed") == 0) or (strcmp($sorder["status"],"open") == 0)){
            $reg_cdate[$currentline] = $sorder["confirmdate"];
	    $reg_status[$currentline] = $sorder["status"];
            foreach($gpd[$user] as $ticket){
                if(strcmp($ticket['orderhash'],$sorder["hashcode"]) == 0){
                    $age = new Age($ticket['birthday']);
                    $plotdata_count[$age->ageConvention]++;
                    echo "\"".$ticket["email"]."\",\n";
                }
            }
            $currentline++;
        }
    }
}

?>
