<?php
# Class for managing htpasswd file
namespace Jonglaria;
require_once("atomicfile.php");

class HtGroupsFile
{
	public function groupExistsHelper($fileHandler, $fileName, $group){
		$fileContentLines = explode("\n", fread($fileHandler, filesize($fileName)));
		foreach ($fileContentLines as $fileContentLine)
		{
			# Empty line (happens for last line break too):
			if($fileContentLine=="") continue;
			# Parse line in the style of "group: user1 user2"
                        $groupAndUsers = explode(':', $fileContentLine);
			$currentGroup = $groupAndUsers[0];
                        $currentUsers = explode(' ', $groupAndUsers[1]);
			# See if group exists already
			if($currentGroup===$group){
				# Group matched line in our user file
				return True;
			}

		}
		return False;
	}
	# Return values:
	# Array with two values: First one if atomic lock worked,
	# second one, if group exists
	public function groupExists($fileName, $group){
		$atomicHandler = new AtomicFile();
		return $atomicHandler->atomicReadAction($fileName, $this, "groupExistsHelper", $group);
	}

	public function userInGroupHelper($fileHandler, $fileName, $args){
		$user = $args[0];
		$group = $args[1];
		$fileContentLines = explode("\n", fread($fileHandler, filesize($fileName)));
		foreach ($fileContentLines as $fileContentLine)
		{
			# Empty line (happens for last line break too):
			if($fileContentLine=="") continue;
			# Parse line in the style of "group: user1 user2"
                        $groupAndUsers = explode(':', $fileContentLine);
			$currentGroup = $groupAndUsers[0];
                        $currentUsers = explode(' ', $groupAndUsers[1]);
			# See if group exists already
			if($currentGroup===$group){
				# User matched line in our user file
				foreach($currentUsers as $currentUser)
				{
					if($currentUser === $user){
						return True;
					}
				}
				return False;
			}

		}
		return False;
	}
	# Return values:
	# Array with two values: First one if atomic lock worked,
	# second one, if group exists
	public function userInGroup($fileName, $user, $group){
		$atomicHandler = new AtomicFile();
		return $atomicHandler->atomicReadAction($fileName, $this, "groupExistsHelper", array($user, $group));
	}

	public function addGroupHelper($fileHandler, $fileName, $group){
		$newFileContent = $group . ": \n";
		$retval = fwrite($fileHandler, $newFileContent);
		if($retval != FALSE){
			return True;
		}
		else{
			return False;
		}

	}
	public function addGroup($fileName, $group){
		$atomicHandler = new AtomicFile();
		return $atomicHandler->atomicAppendAction($fileName, $this, "addGroupHelper", array($group, $encryptedPassword));
	}
	public function addUserToGroupHelper($fileHandler, $fileName, $args){
		$user = $args[0];
		$group = $args[1];
		$fileContentLines = explode("\n", fread($fileHandler, filesize($fileName)));
		$newFileContent = "";
		foreach ($fileContentLines as $fileContentLine)
		{
			# Empty line (happens for last line break too):
			if($fileContentLine=="") continue;
			# Parse line in the style of "username:password"
                        $groupAndUsers = explode(':', $fileContentLine);
			$currentGroup = $groupAndUsers[0];
                        $currentUsers = explode(' ', $groupAndUsers[1]);
			if(strcmp($currentGroup, $group) !== 0){
				# If it is not the correct group don't
				# touch the line:
				$newFileContent .= $fileContentLine . "\n";
			}
			else{
				$newFileContent .= $fileContentLine . " " . $user . "\n";
			}

		}
		$retval = file_put_contents($fileName, $newFileContent);
		if($retval != FALSE){
			return True;
		}
		else{
			return False;
		}

	}
	public function addUserToGroup($fileName, $user, $group){
		$atomicHandler = new AtomicFile();
		return $atomicHandler->atomicReadWriteAction($fileName, $this, "addUserToGroupHelper", array($user, $group));
	}
	public function removeUserFromGroupHelper($fileHandler, $fileName, $args){
		$user = $args[0];
		$group = $args[1];
		$fileContentLines = explode("\n", fread($fileHandler, filesize($fileName)));
		$newFileContent = "";
		foreach ($fileContentLines as $fileContentLine)
		{
			# Empty line (happens for last line break too):
			if($fileContentLine=="") continue;
			# Parse line in the style of "username:password"
                        $groupAndUsers = explode(':', $fileContentLine);
			$currentGroup = $groupAndUsers[0];
                        $currentUsers = explode(' ', $groupAndUsers[1]);
			if(strcmp($currentGroup, $group) !== 0){
				# If it is not the correct group don't
				# touch the line:
				$newFileContent .= $fileContentLine . "\n";
			}
			else{
				foreach($currentUsers as $currentUser){
					if(strcmp($currentUser, $user) !== 0){
						$users .= " " . $currentUser;
					}
				}
				$newFileContent .= $fileContentLine . $users . "\n";
			}

		}
		$retval = file_put_contents($fileName, $newFileContent);
		if($retval != FALSE){
			return True;
		}
		else{
			return False;
		}

	}

	public function removeUserFromGroup($fileName, $user, $group){
		$atomicHandler = new AtomicFile();
		return $atomicHandler->atomicReadWriteAction($fileName, $this, "removeUserFromGroupHelper", array($user, $group));
	}

	public function removeGroupHelper($fileHandler, $fileName, $group){
		$fileContentLines = explode("\n", fread($fileHandler, filesize($fileName)));
		$newFileContent = "";
		foreach ($fileContentLines as $fileContentLine)
		{
			# Empty line (happens for last line break too):
			if($fileContentLine=="") continue;
			# Parse line in the style of "username:password"
                        $groupAndUsers = explode(':', $fileContentLine);
			$currentGroup = $groupAndUsers[0];
                        $currentUsers = explode(' ', $groupAndUsers[1]);
			# Add user if not the one which shall be removed
			if(strcmp($currentGroup, $group) !== 0){
				$newFileContent .= $fileContentLine . "\n";
			}

		}
		$retval = file_put_contents($fileName, $newFileContent);
		if($retval != FALSE){
			return True;
		}
		else{
			return False;
		}

	}

	public function removeGroup($fileName, $group)
	{
		$atomicHandler = new AtomicFile();
		return $atomicHandler->atomicReadWriteAction($fileName, $this, "removeGroupHelper", $group);
	}


}
?>
