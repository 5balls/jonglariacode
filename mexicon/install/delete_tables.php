<?php // delete_tables.php

include("../config/config.php");
include("../inc/database.inc.php");

$DB = new Database("mexicon");

// Database structure
$querys = array("DROP TABLE person;", 
                "DROP TABLE caregiver;",
                "DROP TABLE convention;",
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
