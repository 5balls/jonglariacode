<?php
# Class for all things needed for authorization purposes (without
# passwords and usernames - never add such information here)

# Includes:
# Needed for writing .htaccess and .htgroups files
namespace Jonglaria;
include "atomicfile.php";

class Authorization
{
	# password_hash seems to be the preferred way (compared to
	# older crypt function)
	# We are probably fine with the default salt and cost:
	private function encryptPassword($cleartextPasswd){
		return password_hash($cleartextPasswd, PASSWORD_BCRYPT);
	}
	public function addUser($username,$cleartextPasswd){
		$encryptedPasswd = $this->encryptPassword($cleartextPasswd);
		$atomicHandler = new AtomicFile();
		$atomicHandler->atomicModifyHtPasswdFile(".htusers", $username, $encryptedPasswd);
	}
}
?>
