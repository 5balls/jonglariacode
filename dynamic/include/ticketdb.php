<?php
# Class for accesing the ticket database in a more abstract way
namespace Jonglaria;

require_once("/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/config/mexicon/config.php");
require_once("/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/dynamic/convention/database.inc.php");
require_once("/is/htdocs/wp1110266_HJD5OK7U68/jonglariahidden/dynamic/convention/getAge.inc.php");

class TicketDatabase
{
	private $current_id = 0;
	private $id_info;
	private $db = null;
	private $caregiver;
	public function __construct()
	{
		$this->db = new \Database("db1110266-jonglaria");
	}
	private function refreshInformation($id)
	{
		if(($id != $current_id) || ($current_id == 0))
		{
			$current_id = $id;
			$res = $this->db->query("SELECT * FROM `person` JOIN `convention` ON `person`.`id` = `convention`.`id` WHERE `id` = '".$id."';");
			$this->id_info = $this->db->fetch_assoc($res);
			$res_cg = $this->db->query("SELECT * FROM `caregiver` WHERE `id` = '".$id."'");
			$this->caregiver = $this->db->fetch_assoc($res_cg);
			if(!$this->caregiver) $this->caregiver=null;
		}
	}
  public function insertRegCode($code, $id) 
  {
    $sql = "INSERT person(";
    $sql .= "regcode) VALUES (";
    $sql .= "'". $code ."')";
    $sql .= "WHERE `id` = '".$id."';";
    $this->db->query($sql);
  }
  public function insertPayCode($code, $id) 
  {
    $sql = "INSERT person(";
    $sql .= "paycode) VALUES (";
    $sql .= "'". $code ."')";
    $sql .= "WHERE `id` = '".$id."';";
    $this->db->query($sql);
  }
	public function getFirstName($id)
	{
		$this->refreshInformation($id);	
		return $this->id_info['prename'];
	}
	public function getFamilyName($id)
	{
		$this->refreshInformation($id);	
		return $this->id_info['surname'];
	}
	public function getBirthDate($id)
	{
		$this->refreshInformation($id);	
		// Format birthday nicely:
		$birthday = new \DateTime($this->id_info['birthday'], new \DateTimeZone('UTC'));
		$birthday->setTimezone(new \DateTimeZone('Europe/Berlin'));
		$birthday = $birthday->format("d.m.Y");

		return $birthday;
	}
	public function getSuperVisor($id)
	{
		$this->refreshInformation($id);	
		if($this->caregiver != null)
		{
			return $this->caregiver;
		}
		else
		{
			return "";
		}
	}
	public function getEmail($id)
	{
		$this->refreshInformation($id);	
		return $this->id_info['email'];
	}
	public function getAge($id)
	{
		$this->refreshInformation($id);
		return \getAgeConvention($this->id_info['birthday']);
	}
	public function getCosts($id)
	{
		$age = $this->getAge($id);
		if($age < 12)
		{
			// Todo: We need to handle this specially:
			return "0";
		}
		else if($age <= 16)
		{
			return "20";
		}
		else
		{
			return "28";
		}
	}
}
?>
