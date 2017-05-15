<?php

$redirect_page = 'registration.php';

header('Location: ' .$redirect_page
       .(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''));
exit();
?>
