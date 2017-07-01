<?php
# Class for accesing the ticket database
namespace Jonglaria;

class TicketDatabase
{
	public function getFirstName($id)
	{
		return "Max";
	}
	public function getFamilyName($id)
	{
		return "Mustermann";
	}
	public function getBirthDate($id)
	{
		return "1.4.1990";
	}
	public function getSuperVisor($id)
	{
		return "Maria Mustermutter";
	}
	public function getEmail($id)
	{
		return "fpesth@gmx.de";
	}
}
?>
