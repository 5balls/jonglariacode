<?php // checkConvention.inc.php

function checkConvention($id,$DB) {
  $res = $DB->query("SELECT * FROM `convention` WHERE id=".$id.";");
  if ($person = $DB->fetch_assoc($res)) $convention=true;
  else $convention=false;
  return $convention;
}

?>
