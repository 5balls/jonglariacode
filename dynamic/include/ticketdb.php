<?php
# Class for accesing the ticket database in a more abstract way
# This is a rewrite to make it filebased this year
# TODO There was some confusion when this was merged... probably needs to be rewritten anyway
namespace Jonglaria;

require_once("conventionhelpers.php");

class TicketDatabase
{
	private $current_id = 0;
	private $id_info;
	private $current_id_gala = 0;
	private $id_info_gala;
	private $db = null;
	private $caregiver;
        private $age;
	public function __construct()
	{

	}
	private function refreshInformation($id)
	{
	}
	private function refreshInformationGala($id)
	{
		if(($id != $current_id_gala) || ($current_id_gala == 0))
		{
			$current_id_gala = $id;
			$res = $this->db->query("SELECT * FROM `person` JOIN `galashow` ON `person`.`id` = `galashow`.`id` WHERE `person`.`id` = '".$id."';");
			$this->id_info_gala = $this->db->fetch_assoc($res);
		}
	}
	public function insertRegCode($code, $id) 
	{
		$sql = "UPDATE convention ";
		$sql .= "SET regcode = '".$code;
		$sql .= "' WHERE id = '".$id."';";
		$this->db->query($sql);
	}
	public function insertRegCodeGala($code, $id) 
	{
		$sql = "UPDATE galashow ";
		$sql .= "SET regcode = '".$code;
		$sql .= "' WHERE id = '".$id."';";
		$this->db->query($sql);
	}
	public function insertPayCode($code, $id) 
	{
		$sql = "UPDATE convention ";
		$sql .= "SET paycode = '".$code;
		$sql .= "' WHERE id = '".$id."';";
		$this->db->query($sql);
	}
	public function insertPayCodeGala($code, $id) 
	{
		$sql = "UPDATE galashow ";
		$sql .= "SET paycode = '".$code;
		$sql .= "' WHERE id = '".$id."';";
		$this->db->query($sql);
	}
	public function getFirstName($id)
	{
		$this->refreshInformation($id);	
		return $this->id_info['prename'];
	}
	public function getFirstNameGala($id)
	{
		$this->refreshInformationGala($id);	
		return $this->id_info_gala['prename'];
	}
	public function getFamilyName($id)
	{
		$this->refreshInformation($id);	
		return $this->id_info['surname'];
	}
	public function getFamilyNameGala($id)
	{
		$this->refreshInformationGala($id);	
		return $this->id_info_gala['surname'];
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
	public function getBirthDateGala($id)
	{
		$this->refreshInformationGala($id);	
		// Format birthday nicely:
		$birthday = new \DateTime($this->id_info_gala['birthday'], new \DateTimeZone('UTC'));
		$birthday->setTimezone(new \DateTimeZone('Europe/Berlin'));
		$birthday = $birthday->format("d.m.Y");

		return $birthday;
	}
	public function getSuperVisor($id)
	{
		#todo
		if(($id != $current_id) || ($current_id == 0))
		{
			$current_id = $id;
			$res = $this->db->query("SELECT * FROM `person` JOIN `convention` ON `person`.`id` = `convention`.`id` WHERE `person`.`id` = '".$id."';");
			$this->id_info = $this->db->fetch_assoc($res);
			$res_cg = $this->db->query("SELECT * FROM `caregiver` WHERE `id` = '".$id."';");
			$this->caregiver = $this->db->fetch_assoc($res_cg);
			if(!$this->caregiver) $this->caregiver=null;
		}

	}
	public function getEmail($id)
	{
#todo
	}
	public function getEmailGala($id)
	{
# Todo may not be needed this year (gala is not handled by ticket system this year)
	}
	public function getSuperVisor($id)
	{
		$this->refreshInformation($id);	
		if($this->caregiver != null)
		{
			return $this->caregiver['prename']." ".$this->caregiver['surname'];
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
	public function getEmailGala($id)
	{
		$this->refreshInformationGala($id);	
		return $this->id_info_gala['email'];
	}
	public function getAge($id)
	{
		$this->refreshInformation($id);
		return \getAgeConvention($this->id_info['birthday']);
	}
	public function getAgeGala($id)
	{
		$this->refreshInformation($id);
		return \getAgeConvention($this->id_info_gala['birthday']);
	}
	public function getCosts($id)
	{
		$age = $this->getAge($id);
		if($age < 6)
		{
			// Todo: We need to handle this specially:
			// Actually this case should be caught by the frontpage already
			return "0";
		}
		else if($age < 12)
		{
			return strval($age);
		}
		else if($age < 18)
		{
			return "20";
		}
		else
		{
			return "28";
		}
	}
	// Todo Might add caching later if we need more than one information from database galashow:
	public function getNumberOfTicketsGala($id)
	{
		$res = $this->db->query("SELECT * FROM `person` JOIN `galashow` ON `person`.`id` = `galashow`.`id` WHERE `person`.`id` = '".$id."';");
		$gala_info = $this->db->fetch_assoc($res);
		return $gala_info['ticketcount'];
	}
	// This is only for single gala tickets, gala ticket price is 
	// included in the convention tickets
	public function getCostsGala($id)
	{
		// Todo: Galaprice?
		return strval(($this->getNumberOfTicketsGala($id)*12));
	}
	public function getCostsGalaReduced($id)
	{
		// Todo: Galaprice?
		return strval(($this->getNumberOfTicketsGala($id)*8));
	}

}
?>
