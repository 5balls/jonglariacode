<?php // printBoolValue.inc.php

function printBoolValue($bool)
{
  if ($bool) $print="&#x2714;";
  else $print="&#x2718;";

  return $print;
}

?>
