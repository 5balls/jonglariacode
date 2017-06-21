<?php //config.php

// ############ ERROR REPORTING ############ //
// 0=off // E_ALL=report all errors
error_reporting(E_ALL);

// ############ CHARSET ############ //
// utf-8 // iso-8859-1
define('CHARSET', 'utf-8');

// ############ SLOTS ############ //
// total number of tickets
define('SLOTS', '180'); // mexicon convention
define('GALASLOTS', '700'); // gala show

// ############ DATABASE CONFIG ############ //
define('DB_HOST', 'localhost');
define('DB_USER', 'mexicon');
define('DB_PASSWORD', 'mexicon');

?>
