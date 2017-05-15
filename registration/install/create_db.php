<?php // create_db.php

include("../config/config.php");
include("../inc/database.inc.php");

$DB = new Database("mexicon");

// Database structure
$querys = explode(";",file_get_contents('database_structure.sql')); 
foreach($querys as $sql)
{
  if(strlen($sql)>5)
  {
    $DB->query($sql);
    echo "<b>Executing SQL:</b> ".$sql."<br /><br />";
  }
}

?>
