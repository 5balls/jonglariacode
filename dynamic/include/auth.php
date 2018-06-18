<?php
# Class for all things needed for authorization purposes (without
# passwords and usernames - never add such information here)

# Includes:
# Needed for writing .htaccess and .htgroups files
namespace Jonglaria;
require_once("htpasswd.php");
require_once("htgroups.php");

class Authorization
{
	private $htusers_file;
	private $htgroups_file;
	public function __construct($htusers_file, $htgroups_file){
		$this->htusers_file = $htusers_file;
		$this->htgroups_file = $htgroups_file;
	}
	# password_hash seems to be the preferred way (compared to
	# older crypt function)
	# We are probably fine with the default salt and cost:
	private function encryptPassword($cleartextPasswd){
		return password_hash($cleartextPasswd, PASSWORD_BCRYPT);
	}

	public function removeUser($username){
		$htpasswdHandler = new HtPasswdFile();
		$retValsUserExists = $htpasswdHandler->userExists($this->htusers_file, $username);
		if($retValsUserExists[0] == True){
			# Atomic lock successfull:
			if($retValsUserExists[1] == True){
				# User exists (success case!):
				$retValsRemoveUser = $htpasswdHandler->removeUser($this->htusers_file, $username);
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
				throw new \Exception("USER_DOES_NOT_EXIST");
			}
		}
		else{
			throw new \Exception("SERVER_OVERLOAD");
		}
	}
	public function addUser($username,$cleartextPasswd){
		$htpasswdHandler = new HtPasswdFile();
		$retValsUserExists = $htpasswdHandler->userExists($this->htusers_file, $username);
		if($retValsUserExists[0] == True){
			# Atomic lock successfull:
			if($retValsUserExists[1] == False){
				# User does not exist (success case!):
				$encryptedPasswd = $this->encryptPassword($cleartextPasswd);
				$retValsAddUser = $htpasswdHandler->addUser($this->htusers_file, $username, $encryptedPasswd);
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
	public function addUserToGroup($username, $groupname){
		$htgroupsHandler = new HtGroupsFile();
		$retValsGroupExists = $htgroupsHandler->groupExists($this->htgroups_file, $groupname);
		if($retValsGroupExists[0] == True){
			# Atomic lock successfull:
			if($retValsGroupExists[1] == True){
				# Group does exist (success case!):
				$retValsUserInGroup = $htgroupsHandler->userInGroup($this->htgroups_file, $username, $groupname);
				if($retValsUserInGroup[0] == True){
					# Atomic lock successfull:
					if($retValsUserInGroup[1] == False){
						# User not in group (success case!):
						$retValsAddUserToGroup = $htgroupsHandler->addUserToGroup($this->htgroups_file, $username, $groupname);
						if($retValsAddUserToGroup[0] == True){
							# Atomic lock successfull:
							if($retValsAddUserToGroup[1] == True){
								# Successfully added user to group:
								return True;
							}
							else{
								throw new \Exception("ADDING_USER_TO_GROUP_FAILED");
							}
						}
						else{
							throw new \Exception("SERVER_OVERLOAD");
						}
					}
					else{
						throw new \Exception("USER_ALREADY_IN_GROUP");
					}
				}
				else{
					throw new \Exception("SERVER_OVERLOAD");
				}
			}
			else{
				throw new \Exception("GROUP_DOES_NOT_EXIST");
			}
		}
		else{
			throw new \Exception("SERVER_OVERLOAD");
		}
	}
	public function changePassword($username,$cleartextPasswd){
		$htpasswdHandler = new HtPasswdFile();
		$retValsUserExists = $htpasswdHandler->userExists($this->htusers_file, $username);
		if($retValsUserExists[0] == True){
			# Atomic lock successfull:
			if($retValsUserExists[1] == True){
				# User exists (success case!):
				$encryptedPasswd = $this->encryptPassword($cleartextPasswd);
				$retValsChangePassword = $htpasswdHandler->changePassword($this->htusers_file, $username, $encryptedPasswd);
				if($retValsChangePassword[0] == True){
					# Atomic lock successful:
					if($retValsChangePassword[1] == True){
						# Successfully changed password:
						return True;
					}
					else{
						throw new \Exception("CHANGING_PASSWORD_FAILED");
					}


				}
				else{
					throw new \Exception("SERVER_OVERLOAD");
				}
			}
			else{
				throw new \Exception("USER_DOES_NOT_EXIST");
			}
		}
		else{
			throw new \Exception("SERVER_OVERLOAD");
		}
	}
}
?>
