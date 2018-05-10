<?php
# Class for managing htpasswd file
namespace Jonglaria;
include "atomicfile.php";

class HtPasswdFile
{
	public function userExistsHelper($fileHandler, $fileName, $user){
		$fileContentLines = explode("\n", fread($fileHandler, filesize($fileName)));
		foreach ($fileContentLines as $fileContentLine)
		{
			# Empty line (happens for last line break too):
			if($fileContentLine=="") continue;
			# Parse line in the style of "username:password"
			[$currentUserName, $currentPassword] = explode(':', $fileContentLine);
			# See if user exists already
			if($currentUserName==$user){
				# User matched line in our user file
				return True;
			}

		}
		return False;
	}
	# Return values:
	# Array with two values: First one if atomic lock worked,
	# second one, if user exists
	public function userExists($fileName, $user){
		$atomicHandler = new AtomicFile();
		return $atomicHandler->atomicReadAction($fileName, $this, "userExistsHelper", $user);
	}

	public function addUserHelper($fileHandler, $fileName, $args){
		$userName = $args[0];
		$encryptedPassword = $args[1];
		$newFileContent = $userName . ":" . $encryptedPassword . "\n";
		$retval = fwrite($fileHandler, $newFileContent);
		if($retval != FALSE){
			return True;
		}
		else{
			return False;
		}

	}
	public function addUser($fileName, $username, $encryptedPassword){
		$atomicHandler = new AtomicFile();
		return $atomicHandler->atomicAppendAction($fileName, $this, "addUserHelper", array($username, $encryptedPassword));
	}
	public function removeUserHelper($fileHandler, $fileName, $user){
		$fileContentLines = explode("\n", fread($fileHandler, filesize($fileName)));
		$newFileContent = "";
		foreach ($fileContentLines as $fileContentLine)
		{
			# Empty line (happens for last line break too):
			if($fileContentLine=="") continue;
			# Parse line in the style of "username:password"
			[$currentUserName, $currentPassword] = explode(':', $fileContentLine);
			# Add user if not the one which shall be removed
			if(strcmp($currentUserName, $user) !== 0){
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

	public function removeUser($fileName, $username)
	{
		$atomicHandler = new AtomicFile();
		return $atomicHandler->atomicReadWriteAction($fileName, $this, "removeUserHelper", $username);
	}

}
