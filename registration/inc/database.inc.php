<?php // database.php

class Database
{

  public $db;
  
  public function __construct($name=NULL)
  {
    $this->db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, $name);
    if (mysqli_connect_errno())
      echo "<div style='background-color:#000000;color:#FFFFFF' ><b>DB connection error!</b> <i>".mysqli_connect_error()."</i></div>";
  }
  
  public function query($sql)
  {
    $res = mysqli_query($this->db, $sql);
    if(!$res)
      echo "<div style='background-color:#000000;color:#FFFFFF' ><b>Database error!</b><i>".mysqli_error($this->db)."</i></div>";
    return $res;
  }

  public function num_rows($res) // former db_number
  {
    $num = mysqli_num_rows($res);
    return $num;
  }

  public function fetch_assoc($res) // former db_dataset
  {
    $data = mysqli_fetch_assoc($res);
    return $data;
  }

  public function fetch_row($res)
  {
    $data = mysqli_fetch_row($res);
    return $data;
  }

  public function escape_string($esc_string) // former db_escape
  {
    $esc_string = mysqli_escape_string($esc_string);
    return $esc_string;
  }

}

?>
