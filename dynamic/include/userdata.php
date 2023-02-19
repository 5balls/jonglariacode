<?php
namespace Jonglaria;
require_once("config.php");
require_once("atomicfile.php");

class Data
{
	public $objectName;
	public $expires;
	public $reasonForStoring;
	public $dataContent;
	public $accessGroups;
	public function __construct($objectName, $expires, $reasonForStoring, $dataContent, $accessGroups){
		$this->objectName = $objectName;
		$this->expires = $expires;
		$this->reasonForStoring = $reasonForStoring;
		$this->dataContent = $dataContent;
		$this->accessGroups = $accessGroups;
	}
}

# Manages User data in JSON files in a seperate directory
class UserData 
{
        protected $pathName;
	protected $fileName;
	protected $userName;
	protected $dataObjects = array();
	protected $cfg;
	# Creates a new object:
	public function __construct($fn, $un){
		$this->cfg = new Config();
                #$this->pathName = $pn."/".$un."/";
# TODO Create dir
		$this->fileName = $fn;
		$this->userName = $un;
	}
	# Reads from file:

	public function readDataHelper($fileHandler, $fileName, $args){
		$encoded_data = fread($fileHandler, filesize($fileName));
		$decoded_data = json_decode($encoded_data, true);
		$this->userName = $decoded_data["userName"];
		foreach($decoded_data["data"] as $decoded_datum){
			$this->addDataObject($decoded_datum["objectName"], $decoded_datum["expires"], $decoded_datum["reasonForStoring"], $decoded_datum["dataContent"], $decoded_datum["accessGroups"]);
		}

	}
	public function fillFromFile(){
		$atomicHandler = new AtomicFile();
		$atomicHandler->atomicReadAction($this->fileName, $this, "readDataHelper", "test");
	}
	public function storeDataHelper($fileHandler, $fileName, $args){
		$encoded_data = json_encode(array("userName" => $this->userName, "data" => $this->dataObjects));	
		fwrite($fileHandler, $encoded_data);
	}
	public function storeData(){
		$atomicHandler = new AtomicFile();
		return $atomicHandler->atomicWriteAction($this->fileName, $this, "storeDataHelper", "test");
	}
	public function addDataObject($objectName, $expires, $reasonForStoring, $content, $access){
		$newDataObject = new Data($objectName, $expires, $reasonForStoring, $content, $access);
		return array_push($this->dataObjects, $newDataObject);
	}
        public function addDataObjectD($dataobject){
            return array_push($this->dataObjects, $dataobject);
        }
	public function getDataObject($objectName){
		foreach ($this->dataObjects as $dataObject)
		{
			if(strcmp($dataObject->objectName, $objectName) == 0){
				return $dataObject;
			}
		}
		return NULL;
	}
        public function getListOfDataObjectNames(){
            $dataobjects = array();
            foreach($this->dataObjects as $dataObject){
                $dataobjects[] = $dataObject->objectName;
            }
            return $dataobjects;
        }
}

class UserDataSQL extends UserData {
	public static $dbconn = NULL;
	protected $dataBaseTable;
	public function __construct($fn, $un){
		parent::__construct($fn, $un);
		$this->dataBaseTable = pathinfo($this->fileName)['filename'];
		try {
			if(is_null(self::$dbconn)){
				self::$dbconn = new \PDO($this->cfg['database_dsn'], $this->cfg['database_user'], $this->cfg['database_password']);
				self::$dbconn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); 
			}
		}
		catch(PDOException $e)
		{
			error_log("MySQL connection failed: " . $e->getMessage(), 0);
		}
	}
	public function __destruct(){
		//error_log("Destructor UserdataSQL", 0);
		//$this->dbconn = NULL;
	}
	public function fillFromFile(){
		$sql_string = "SELECT data FROM convention.".$this->dataBaseTable." WHERE username ='". $this->userName . "'";
		try {
			$fillquery = self::$dbconn->query($sql_string);
			foreach($fillquery as $encoded_data)
			{
				$decoded_data = json_decode($encoded_data["data"], true);
				$this->userName = $decoded_data["userName"];
				foreach($decoded_data["data"] as $decoded_datum){
					$this->addDataObject($decoded_datum["objectName"], $decoded_datum["expires"], $decoded_datum["reasonForStoring"], $decoded_datum["dataContent"], $decoded_datum["accessGroups"]);
				}
			}
			$fillquery = NULL;
		}
		catch(PDOException $e)
		{
			error_log("MySQL command failed: " . $e->getMessage(), 0);
		}
		
	}
	public function storeData(){
		$encoded_data = json_encode(array("userName" => $this->userName, "data" => $this->dataObjects), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);	
		# Check if we need to update dataset or insert new dataset
		$sql_string = "SELECT COUNT(*) FROM convention.".$this->dataBaseTable." WHERE username ='". $this->userName . "'";
		try {
		    $result = self::$dbconn->query($sql_string);

			if($result->fetchColumn()>0){
			    $sql_string = "UPDATE convention.".$this->dataBaseTable." SET data='". $encoded_data . "' WHERE username='". $this->userName . "'";

			}
			else{
			    $sql_string = "INSERT INTO convention.".$this->dataBaseTable." (username, data) values ('". $this->userName . "', '". $encoded_data . "')";
			}
		}
		catch(PDOException $e)
		{
			error_log("MySQL command failed: " . $e->getMessage(), 0);
			return;
		}
		try {
			self::$dbconn->exec($sql_string);
		}
		catch(PDOException $e)
		{
			error_log("MySQL command failed: " . $e->getMessage(), 0);
			return;
		}
	}
	public function getTableList(){
		$sql_string = "show tables in convention";
		$tables_array = array();
		try {
			foreach(self::$dbconn->query($sql_string) as $row)
			{
				$tables_array[] = $row["Tables_in_convention"];
			}

		}
		catch(PDOException $e)
		{
			error_log("MySQL command failed: " . $e->getMessage(), 0);
		}
		return $tables_array;
	}
}

class CollectedUserData{
    public $userData = array();
    public function __construct($base_path, $filename){
        # TODO replace / by generic path seperator
        $userfiles = explode("\n",shell_exec("ls ".$base_path."/*/".$filename));
        foreach($userfiles as $userfile){
            $user = explode("/", $userfile)[7];
            $userdata = new UserData($userfile, $user);
            $userdata->fillFromFile();
            $this->userData[$user] = $userdata;
	    $userdata = NULL;
        }
    }
}

class CollectedUserDataSQL{
    public $userData = array();
    protected $dbconn;
    protected $dataBaseTable;
    public function __construct($base_path, $filename){
        try {
            $this->dbconn = new \PDO($this->cfg['database_dsn'], $this->cfg['database_user'], $this->cfg['database_password']);
            $this->dbconn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); 
        }
        catch(PDOException $e)
        {
            error_log("MySQL connection failed: " . $e->getMessage(), 0);
	    print("MySQL connection failed: " . $e->getMessage());
        }
        $sql_string = "SELECT username FROM convention.".$filename;
        try {
            $users_qr = $this->dbconn->query($sql_string);
        } 
        catch(PDOException $e)
        {
            error_log("MySQL command failed: " . $e->getMessage(), 0);
print("MySQL command failed: " . $e->getMessage());
        }
        foreach($users_qr as $users_qr_row){
            $users[] = $users_qr_row["username"];
        }
        foreach($users as $user){
            $userdata = new UserDataSQL($filename, $user);
            $userdata->fillFromFile();
            $this->userData[$user] = $userdata;
        }
    }
}
?>
