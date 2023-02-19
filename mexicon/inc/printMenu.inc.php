<?php // printBoolValue.inc.php

function printMenu()
{
  $print = "<div id='menu'>\n";
  $print .= "<a href='./redirect.php?page=mexicon'>Convention</a>";
  $print .= "|";
  $print .= "<a href='./redirect.php?page=gala'>Galashow</a>";
  $print .= "</div>";

  return $print;
}

?>
