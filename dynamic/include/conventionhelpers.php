<?php
namespace Jonglaria;
require_once("config.php");
require_once("userdata.php");

function calculateConventionPrice($age, $preregperiod){
    if($preregperiod){
        if($age < 6){
            return 0;
        }
        if($age < 12){
            return $age;
        }
        if($age < 18){
            return 20;
        }
        return 28;
    }
    else{
        if($age < 6){
            return 0;
        }
        if($age < 12){
            return $age*2;
        }
        if($age < 18){
            return 25;
        }
        return 35;
    }
}

class UserDataC implements \ArrayAccess{
    protected $username;
    protected $filename;
    protected $userData;
    protected $cfg;
    public $nofile;
    public function __construct($data){
        $this->cfg = new Config();
        $this->nofile = False;
        $this->username = $_SERVER['PHP_AUTH_USER'];
        $this->filename = $this->cfg['userdata_path'].'/'.$this->username.'/'.$data.'.json';
        if(!file_exists($this->filename)){
            $this->nofile = True;
            return;
        }
        $this->userData = new UserData($this->filename, $this->username);
        $this->userData->fillFromFile();
    }
    public function offsetSet($offset, $value){
        # TODO Currently only for reading
    }
    public function offsetGet($offset){
        return $this->userData->getDataObject($offset)->dataContent;
    }
    public function offsetExists($offset){
        return NULL != $this->userData->getDataObject($offset);
    }
    public function offsetUnset($offset){
        # TODO
    }
}

class UserDataCU extends UserDataC{
    public function __construct($data, $username){
        $this->filename = $this->cfg['userdata_path'].'/'.$username.'/'.$data.'.json';
        if(!file_exists($this->filename))
            return;
        $this->userData = new UserData($this->filename, $username);
        $this->userData->fillFromFile();
    }
}

class Age{
    private $username;
    private $filename_personaldata;
    public $currentAge;
    public $ageConvention;
    public $ageGala;
    public $couldreaddata;
    protected $cfg;
    public function __construct($birthday = null){
        $this->cfg = new Config();
	if(is_null($birthday)){
	    $this->couldreaddata = False;
	    $this->username = $_SERVER['PHP_AUTH_USER'];
	    $this->filename_personaldata = $this->cfg['userdata_path'].'/'.$this->username.'/personaldata.json';
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
	else{
	    $this->couldreaddata = False;
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
        return date_diff(date_create($birthday), date_create($this->cfg['convention_start_date']))->y;
    }

    private function getAgeGala($birthday)
    {
        return date_diff(date_create($birthday), date_create($this->cfg['convention_show_date']))->y;
    }

}
?>
