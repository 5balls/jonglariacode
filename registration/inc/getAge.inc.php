<?php // getAge.inc.php

function getAge($birthday)
{
  return date_diff(date_create($birthday), date_create('now'))->y;
}

function getAgeConvention($birthday)
{
  return date_diff(date_create($birthday), date_create('2017-09-15'))->y;
}

?>
