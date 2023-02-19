<?php
namespace Jonglaria;

require_once("config.php");
require_once("form.php");
require_once("userdata.php");
require_once("auth.php");
require_once("conventionhelpers.php");

$cfg = new Config();

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
        if((strcmp($sorder["status"],"confirmed") == 0) or (strcmp($sorder["status"],"paymentconfirmed") == 0)){
            $plotdata_cdate[$currentline] = $sorder["confirmdate"];
            foreach($gpd[$user] as $ticket){
                if(strcmp($ticket['orderhash'],$sorder["hashcode"]) == 0){
                    $plotdata_count[$currentline]++;
                }
            }
            $currentline++;
        }
    }
}

array_multisort($plotdata_cdate, $plotdata_count);

$currentline = 0;
$currentregs = 0;
foreach($plotdata_cdate as $date){
    $currentregs += $plotdata_count[$currentline];
    $data.=$date." ".$currentregs."\n";
    $currentline++;
}

$cmd = $cfg['dynamic_path']."/plot_ordertime.sh";

$descriptorspec = array(
   0 => array("pipe", "r"),
   1 => array("pipe", "w")
);

$process = proc_open($cmd, $descriptorspec, $pipes);

if (is_resource($process)) {
    fwrite($pipes[0], $data);
    fclose($pipes[0]);

    $png_data = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    proc_close($process);

    header('Content-Type: image/png');
    echo $png_data;
}
?>
