<?php

if (isset($_GET['page'])) {
  if ($_GET['page'] == 'mexicon')
    $redirect_page = 'registration.php';
  else if ($_GET['page'] == 'gala')
    $redirect_page = 'galatickets.php';
}
else
  $redirect_page = 'registration.php';

header('Location: ' .$redirect_page
       .(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''));
exit();
?>
