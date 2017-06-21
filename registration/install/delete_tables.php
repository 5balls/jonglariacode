<?php // delete_tables.php

include("../config/config.php");
include("../inc/database.inc.php");

$DB = new Database("mexicon");

// Database structure
$querys = array("DROP TABLE participants;", 
                "DROP TABLE galashow;");
foreach($querys as $sql)
{
  if(strlen($sql)>5)
  {
    $DB->query($sql);
    echo "<b>Executing SQL:</b> ".$sql."<br /><br />";
  }
}

?>
