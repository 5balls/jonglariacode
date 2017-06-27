<?php

include("../config/config.php");
include("../inc/database.inc.php");
include("../inc/getAge.inc.php");
include("../inc/checkConvention.inc.php");

$DB = new Database("mexicon");  

if (isset($_GET['id'])) {
  $res = $DB->query("SELECT * FROM `person` WHERE `id` = '".$_GET['id']."';");
  if ($person = $DB->fetch_assoc($res)) {
    if ($_GET['payed']) {
      if ($_GET['payed'] == "true" || $_GET['payed'] == 1) {
        if (checkConvention($person['id'], $DB)) {
          $sql = "UPDATE `convention` SET `payed` = '1' WHERE `id` = '".$person['id']."';";
          $DB->query($sql);
        }
        $sql = "UPDATE `galashow` SET `payed` = '1' WHERE `id` = '".$person['id']."';";
        $DB->query($sql);
      }
      else if ($_GET['payed'] == "false" || $_GET['payed'] == 0) {
        if (checkConvention($person['id'], $DB)) {
          $sql = "UPDATE `convention` SET `payed` = '0' WHERE `id` = '".$person['id']."';";
          $DB->query($sql);
        }
        $sql = "UPDATE `galashow` SET `payed` = '0' WHERE `id` = '".$person['id']."';";
        $DB->query($sql);
      }
    }
    if (isset($_GET['arrived'])) {
      if ($_GET['arrived'] == "true" || $_GET['arrived'] == 1) {
        $arrivaltime = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone('UTC')); 
        $sql = "UPDATE `galashow` SET `arrivaltime` = '".$arrivaltime->format("Y-m-d H:i:s")."' WHERE `id` = '".$person['id']."';";
        $DB->query($sql);
      }
      else if ($_GET['arrived'] == "false" || $_GET['arrived'] == 0) {
        $sql = "UPDATE `galashow` SET `arrivaltime` = NULL WHERE `id` = '".$person['id']."';";
        $DB->query($sql);
      }
    }
  }
}
else if (isset($_POST['update'])) {
  // selection has payed
  if ($_POST['update'] == 'payed') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        if (checkConvention($id, $DB)) {
          $sql = "UPDATE `convention` SET `payed` = '1' WHERE `id` = '".$id."';";
          $DB->query($sql);
        }
        $sql = "UPDATE `galashow` SET `payed` = '1' WHERE `id` = '".$id."';";
        $DB->query($sql);
      }
    }
  }
  // selection has arrived
  if ($_POST['update'] == 'arrived') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        $arrivaltime = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone('UTC')); 
        $sql = "UPDATE `galashow` SET `arrivaltime` = '".$arrivaltime->format("Y-m-d H:i:s")."' WHERE `id` = '".$id."';";
        $DB->query($sql);
      }
    }
  }
  // selection has *not* payed
  else if ($_POST['update'] == 'notpayed') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        if (checkConvention($id, $DB)) {
          $sql = "UPDATE `convention` SET `payed` = '0' WHERE `id` = '".$id."';";
          $DB->query($sql);
        }
        $sql = "UPDATE `galashow` SET `payed` = '0' WHERE `id` = '".$id."';";
        $DB->query($sql);
      }
    }
  }
  // selection has not arrived
  if ($_POST['update'] == 'notarrived') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        $sql = "UPDATE `galashow` SET `arrivaltime` = NULL WHERE `id` = '".$id."';";
        $DB->query($sql);
      }
    }
  }
  // delete selection 
  else if ($_POST['update'] == 'delete') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        $sql = "DELETE FROM `person` WHERE `id` = '".$id."';";
        $DB->query($sql);
        if (checkConvention($id, $DB)) {
          $sql = "DELETE FROM `convention` WHERE `id` = '".$id."';";
          $DB->query($sql);
        }
        $sql = "DELETE FROM `galashow` WHERE `id` = '".$id."';";
        $DB->query($sql);
      }
    }
  }
}

$redirect_page = 'galashow.php';
header('Location: ' .$redirect_page);
exit();

?>
