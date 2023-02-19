<?php

if (isset($_GET['page']) || isset($_GET['mexicon']) || isset($_GET['gala'])) {
  if ($_GET['page'] == 'mexicon' || isset($_GET['mexicon']))
    $redirect_page = 'index.php';
  else if ($_GET['page'] == 'gala' || isset($_GET['gala']))
    $redirect_page = 'galashow.php';
}
else
  $redirect_page = 'index.php';

//header('Location: ' .$redirect_page
//       .(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''));
header('Location: ' .$redirect_page);
exit();

?>
