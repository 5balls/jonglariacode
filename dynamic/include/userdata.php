<?php
namespace Jonglaria;
include "atomicfile.php";

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
	private $fileName;
	private $userName;
	private $dataObjects = array();
	# Creates a new object:
	public function __construct($fn, $un){
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
	public function getDataObject($objectName){
		foreach ($this->dataObjects as $dataObject)
		{
			if(strcmp($dataObject->objectName, $objectName) == 0){
				return $dataObject;
			}
		}
		return NULL;
	}
}
?>
