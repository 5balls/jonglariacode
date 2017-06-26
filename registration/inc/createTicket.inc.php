<?php // createTicket.inc.php
// this function needs a 'tickets/' subdirectory in the BASEDIR
// with ticket pdf creation files 

function createTicket($id,$DB) {
  $res = $DB->query("SELECT * FROM `participants` WHERE id=".$id.";");
  $data = $DB->fetch_assoc($res);
  $regtime = new DateTime($data['regtime'], new DateTimeZone('UTC'));
  $regtime->setTimezone(new DateTimeZone('Europe/Berlin'));
  $regtime = $regtime->format("d.m.Y");

  exec("LD_LIBRARY_PATH=$(pwd):".'$LD_LIBRARY_PATH'." ".$_SERVER['DOCUMENT_ROOT'].BASEDIR."/tickets/example1"
      ." -1".$data['prename']
      ." -2".$data['surname']
      ." -b".$regtime
      ." -s\"Matilda Mustermutter\""
      ." -i\"http://jonglaria.org/reg/f458c955\""
      ." > ".$data['id'].".ps", $ps_output, $retval);
  exec("TEMP=".$_SERVER['DOCUMENT_ROOT'].BASEDIR." gs"
      ." -dBATCH -dNOPAUSE -sPAPERSIZE=a4 -dNOEPS -dAutoFilterColorImages=false"
      ." -dColorImageFilter=/FlateEncode -sDEVICE=pdfwrite"
      ." -o ".$data['id'].".pdf"
      ." -f ".$data['id'].".ps"
      ." -c \"[ /Author (Jonglaria e.V.) /Subject (Electronic Ticket)"
      ." /Keywords (Convention, Tuebingen, 2017, Juggling, Jonglaria) /DOCINFO pdfmark\"", $ps_output, $retval);
}

?>
