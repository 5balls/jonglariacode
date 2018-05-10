<?php
namespace Jonglaria;
include "atomicfile.php";

class Data
{
	public $objectName;
	public $expires;
	public $reasonForStoring;
	public $dataContent;
	public function __construct($objectName, $expires, $reasonForStoring, $dataContent){
		$this->objectName = $objectName;
		$this->expires = $expires;
		$this->reasonForStoring = $reasonForStoring;
		$this->dataContent = $dataContent;
	}
}

# Manages User data in JSON files in a seperate directory
class UserData 
{
	private $fileName;
	private $userName;
	private $dataObjects = array();
	# Creates a new object:
	function __construct($fn, $un){
		$this->fileName = $fn;
		$this->userName = $un;
	}
	# Constructs from file:
	#public function __construct($fileName){
		# TODO
	#}
	public function storeDataHelper($fileHandler, $fileName, $args){
		$encoded_data = json_encode(array("userName" => $this->userName, "data" => $this->dataObjects));	
		fwrite($fileHandler, $encoded_data);
	}
	public function storeData(){
		$atomicHandler = new AtomicFile();
print "<h1>".$this->fileName."</h1>";
		return $atomicHandler->atomicWriteAction($this->fileName, $this, "storeDataHelper", "test");
	}
	public function addDataObject($objectName, $expires, $reasonForStoring, $content){
		$newDataObject = new Data($objectName, $expires, $reasonForStoring, $content);
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
