<?php
# Class for all things needed for authorization purposes (without
# passwords and usernames - never add such information here)

# Includes:
# Needed for writing .htaccess and .htgroups files
namespace Jonglaria;
include "htpasswd.php";

class Authorization
{
	# password_hash seems to be the preferred way (compared to
	# older crypt function)
	# We are probably fine with the default salt and cost:
	private function encryptPassword($cleartextPasswd){
		return password_hash($cleartextPasswd, PASSWORD_BCRYPT);
	}

	public function removeUser($username){
		$htpasswdHandler = new HtPasswdFile();
		$retValsUserExists = $htpasswdHandler->userExists(".htusers", $username);
		if($retValsUserExists[0] == True){
			# Atomic lock successfull:
			if($retValsUserExists[1] == True){
				# User exists (success case!):
				$retValsRemoveUser = $htpasswdHandler->removeUser(".htusers", $username);
				if($retValsRemoveUser[0] == True){
					# Atomic lock successful:
					if($retValsRemoveUser[1] == True){
						# Successfully added new user:
						return True;
					}
					else{
						throw new \Exception("REMOVING_USER_FAILED");
					}


				}
				else{
					throw new \Exception("SERVER_OVERLOAD");
				}
			}
			else{
				throw new \Exception("USER_DOES_NOT_EXISTS");
			}
		}
		else{
			throw new \Exception("SERVER_OVERLOAD");
		}
	}
	public function addUser($username,$cleartextPasswd){
		$htpasswdHandler = new HtPasswdFile();
		$retValsUserExists = $htpasswdHandler->userExists(".htusers", $username);
		if($retValsUserExists[0] == True){
			# Atomic lock successfull:
			if($retValsUserExists[1] == False){
				# User does not exist (success case!):
				$encryptedPasswd = $this->encryptPassword($cleartextPasswd);
				$retValsAddUser = $htpasswdHandler->addUser(".htusers", $username, $encryptedPasswd);
				if($retValsAddUser[0] == True){
					# Atomic lock successful:
					if($retValsAddUser[1] == True){
						# Successfully added new user:
						return True;
					}
					else{
						throw new \Exception("ADDING_USER_FAILED");
					}


				}
				else{
					throw new \Exception("SERVER_OVERLOAD");
				}
			}
			else{
				throw new \Exception("USER_EXISTS");
			}
		}
		else{
			throw new \Exception("SERVER_OVERLOAD");
		}
	}
}
?>
