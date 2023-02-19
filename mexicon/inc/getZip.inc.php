<?php // getZip.inc.php

function getZip($ip=NULL)
{
  if (is_null($ip)) $ip = $_SERVER['REMOTE_ADDR'];
  $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
  if (isset($details->postal))
    return "{$details->postal}";
  else
    return null;
}

?>
