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

$plotdata_count = array();


foreach($gorders as $user=>$order){
    foreach($order as $sorder){
        if((strcmp($sorder["status"],"confirmed") == 0) or (strcmp($sorder["status"],"paymentconfirmed") == 0)){
            foreach($gpd[$user] as $ticket){
                if(strcmp($ticket['orderhash'],$sorder["hashcode"]) == 0){
                    $age = new Age($ticket['birthday']);
                    $plotdata_count[$age->ageConvention]++;
                }
            }
        }
    }
}
ksort($plotdata_count);

file_put_contents($cfg['statistics_file'], print_r($plotdata_count, true));

$binwidth = 5;
$bin = $binwidth;
$agecountbin = 0;
foreach($plotdata_count as $age=>$agecount){
    if($age > $bin){
        $data.=($bin-($binwidth/2.0))." ".$agecountbin."\n";
        while($age > $bin) $bin += $binwidth;
        $agecountbin = 0;
    }
    $agecountbin += $agecount;
}
$data.=($bin-($binwidth/2.0))." ".$agecountbin."\n";

$cmd = $cfg['dynamic_path']."/plot_age.sh";

$descriptorspec = array(
   0 => array("pipe", "r"),
   1 => array("pipe", "w")
);

file_put_contents($cfg['statistics_age_file'], $data);

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
