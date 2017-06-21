<?php

include("../config/config.php");
include("../inc/database.inc.php");
include("../inc/getAge.inc.php");

$DB = new Database("mexicon");  

if (isset($_GET['id'])) {
  $res = $DB->query("SELECT * FROM `participants` WHERE `id` = '".$_GET['id']."';");
  $data = $DB->fetch_assoc($res);
  if ($_GET['payed']) {
    if ($_GET['payed'] == "true") {
      $sql = "UPDATE `participants` SET `payed` = '1' WHERE `id` = '".$_GET['id']."';";
      $DB->query($sql);
      $sql = "UPDATE `galashow` SET `payed` = '1' WHERE `participant_id` = '".$_GET['id']."';";
      $DB->query($sql);
    }
    else if ($_GET['payed'] == "false") {
      $sql = "UPDATE `participants` SET `payed` = '0' WHERE `id` = '".$_GET['id']."';";
      $DB->query($sql);
      $sql = "UPDATE `galashow` SET `payed` = '0' WHERE `participant_id` = '".$_GET['id']."';";
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
      }
    }
  }
  // selection has *not* payed
  if ($_POST['update'] == 'notpayed') {
    if(!empty($_POST['id_list'])) {
      foreach($_POST['id_list'] as $id) {
        $sql = "UPDATE `participants` SET `payed` = '0' WHERE `id` = '".$id."';";
        $DB->query($sql);
        $sql = "UPDATE `galashow` SET `payed` = '0' WHERE `participant_id` = '".$id."';";
        $DB->query($sql);
      }
    }
  }
  // delete selection 
  if ($_POST['update'] == 'delete') {
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
