<?php
# Class for accesing the ticket database in a more abstract way
# This is a rewrite to make it filebased this year
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
	}
	public function insertRegCode($code, $id) 
	{
# Todo save to file:
	}
	public function insertRegCodeGala($code, $id) 
	{
# Todo may not be needed this year (gala is not handled by ticket system this year)
	}
	public function insertPayCode($code, $id) 
	{
#TODO important
	}
	public function insertPayCodeGala($code, $id) 
	{
# Todo may not be needed this year (gala is not handled by ticket system this year)
	}
	public function getFirstName($id)
	{
#todo
	}
	public function getFirstNameGala($id)
	{
# Todo may not be needed this year (gala is not handled by ticket system this year)
	}
	public function getFamilyName($id)
	{
#todo
	}
	public function getFamilyNameGala($id)
	{
# Todo may not be needed this year (gala is not handled by ticket system this year)
	}
	public function getBirthDate($id)
	{
#todo
		$this->refreshInformation($id);	
		// Format birthday nicely:
		$birthday = new \DateTime($this->id_info['birthday'], new \DateTimeZone('UTC'));
		$birthday->setTimezone(new \DateTimeZone('Europe/Berlin'));
		$birthday = $birthday->format("d.m.Y");

		return $birthday;
	}
	public function getBirthDateGala($id)
	{
# Todo may not be needed this year (gala is not handled by ticket system this year)
	}
	public function getSuperVisor($id)
	{
#todo
	}
	public function getEmail($id)
	{
#todo
	}
	public function getEmailGala($id)
	{
# Todo may not be needed this year (gala is not handled by ticket system this year)
	}
	public function getAge($id)
	{
#todo
	}
	public function getAgeGala($id)
	{
# Todo may not be needed this year (gala is not handled by ticket system this year)
	}
	public function getCosts($id)
	{
#todo
	}
	// Todo Might add caching later if we need more than one information from database galashow:
	public function getNumberOfTicketsGala($id)
	{
#todo
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
