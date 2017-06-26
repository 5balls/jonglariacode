<?php

include("../config/config.php");
include("../inc/database.inc.php");
include("../inc/getAge.inc.php");
include("../inc/createTicket.inc.php");

$DB = new Database("mexicon");  

if (isset($_GET['id'])) {
  //$res = $DB->query("SELECT * FROM `participants` WHERE `id` = '".$_GET['id']."';");
  //$data = $DB->fetch_assoc($res);
  if (isset($_GET['payed'])) {
    if ($_GET['payed'] == "true" || $_GET['payed'] == 1) {
      $sql = "UPDATE `participants` SET `payed` = '1' WHERE `id` = '".$_GET['id']."';";
      $DB->query($sql);
      $sql = "UPDATE `galashow` SET `payed` = '1' WHERE `participant_id` = '".$_GET['id']."';";
      $DB->query($sql);
      createTicket($_GET['id'], $DB); 
    }
    else if ($_GET['payed'] == "false" || $_GET['payed'] == 0) {
      $sql = "UPDATE `participants` SET `payed` = '0' WHERE `id` = '".$_GET['id']."';";
      $DB->query($sql);
      $sql = "UPDATE `galashow` SET `payed` = '0' WHERE `participant_id` = '".$_GET['id']."';";
      $DB->query($sql);
    }
  }
  if (isset($_GET['arrived'])) {
    if ($_GET['arrived'] == "true" || $_GET['arrived'] == 1) {
      $datetimenow = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone('UTC')); 
      $sql = "UPDATE `participants` SET `arrivaltime` = '".$datetimenow->format("Y-m-d H:i:s")."' WHERE `id` = '".$_GET['id']."';";
      $DB->query($sql);
      $sql = "UPDATE `galashow` SET `arrivaltime` = '".$datetimenow->format("Y-m-d H:i:s")."' WHERE `participant_id` = '".$_GET['id']."';";
      $DB->query($sql);
    }
    else if ($_GET['arrived'] == "false" || $_GET['arrived'] == 0) {
      $sql = "UPDATE `participants` SET `arrivaltime` = NULL WHERE `id` = '".$_GET['id']."';";
      $DB->query($sql);
      $sql = "UPDATE `galashow` SET `arrivaltime` = NULL WHERE `participant_id` = '".$_GET['id']."';";
      $DB->query($sql);
    }
  }
}
else if (isset($_POST['update'])) {
  // selection has payed
  if ($_POST['update'] == 'payed') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        $sql = "UPDATE `participants` SET `payed` = '1' WHERE `id` = '".$id."';";
        $DB->query($sql);
        $sql = "UPDATE `galashow` SET `payed` = '1' WHERE `participant_id` = '".$id."';";
        $DB->query($sql);
        createTicket($id, $DB); 
      }
    }
  }
  // selection has arrived
  if ($_POST['update'] == 'arrived') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        $datetimenow = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone('UTC')); 
        $sql = "UPDATE `participants` SET `arrivaltime` = '".$datetimenow->format("Y-m-d H:i:s")."' WHERE `id` = '".$id."';";
        $DB->query($sql);
        $sql = "UPDATE `galashow` SET `arrivaltime` = '".$datetimenow->format("Y-m-d H:i:s")."' WHERE `participant_id` = '".$id."';";
        $DB->query($sql);
      }
    }
  }
  // selection has *not* payed
  else if ($_POST['update'] == 'notpayed') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        $sql = "UPDATE `participants` SET `payed` = '0' WHERE `id` = '".$id."';";
        $DB->query($sql);
        $sql = "UPDATE `galashow` SET `payed` = '0' WHERE `participant_id` = '".$id."';";
        $DB->query($sql);
      }
    }
  }
  // selection has not arrived
  if ($_POST['update'] == 'notarrived') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        $datetimenow = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone('UTC')); 
        $sql = "UPDATE `participants` SET `arrivaltime` = NULL WHERE `id` = '".$id."';";
        $DB->query($sql);
        $sql = "UPDATE `galashow` SET `arrivaltime` = NULL WHERE `participant_id` = '".$id."';";
        $DB->query($sql);
      }
    }
  }
  // delete selection 
  else if ($_POST['update'] == 'delete') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        $sql = "DELETE FROM `participants` WHERE `id` = '".$id."';";
        $DB->query($sql);
        $sql = "DELETE FROM `galashow` WHERE `participant_id` = '".$id."';";
        $DB->query($sql);
      }
    }
  }
}

$redirect_page = 'participants.php';
header('Location: ' .$redirect_page);
exit();

?>
