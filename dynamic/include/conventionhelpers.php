<?php
namespace Jonglaria;
require_once("userdata.php");

class Age{
    private $username;
    private $filename_personaldata;
    public $currentAge;
    public $ageConvention;
    public $ageGala;
    public $couldreaddata;
    public function __construct(){
        $this->couldreaddata = False;
        $this->username = $_SERVER['PHP_AUTH_USER'];
        $this->filename_personaldata = '/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/userdata/'.$this->username.'/personaldata.json';
        if(file_exists($this->filename_personaldata)){
            $data = new UserData($this->filename_personaldata, $this->username);
            $data->fillFromFile();
            $regperson = $data->getDataObject('regperson');
            $birthday = $regperson->dataContent['birthday'];
            $this->currentAge = $this->getAge($birthday);
            $this->ageConvention = $this->getAgeConvention($birthday);
            $this->ageGala = $this->getAgeGala($birthday);
            $this->couldreaddata = True;
        }
    }
    private function getAge($birthday)
    {
        return date_diff(date_create($birthday), date_create('now'))->y;
    }

    private function getAgeConvention($birthday)
    {
        return date_diff(date_create($birthday), date_create('2018-09-14'))->y;
    }

    private function getAgeGala($birthday)
    {
        return date_diff(date_create($birthday), date_create('2018-09-15'))->y;
    }

}
?>
