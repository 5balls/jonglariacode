<?php

include("config/config.php");
include("inc/database.inc.php");

$DB = new Database("mexicon");  

$res = $DB->query("SELECT * FROM `participants`;");
while ($data = $DB->fetch_assoc($res)) {
  echo $data['id'];
  echo ",";
  echo $data['prename'];
  echo ",";
  echo $data['surname'];
  echo "\n";
}

?>
