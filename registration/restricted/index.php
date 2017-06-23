<?php

if (isset($_GET['page'])) {
  if ($_GET['page'] == 'mexicon')
    $redirect_page = 'participants.php';
  else if ($_GET['page'] == 'gala')
    $redirect_page = 'galashow.php';
}
else
  $redirect_page = 'participants.php';

header('Location: ' .$redirect_page
       .(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''));
exit();
?>
