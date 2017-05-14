<?php
# Class for atomic file handling (should be used only where really
# needed)
namespace Jonglaria;

class AtomicFile
{
	# From http;//php.net/manual/en/function.flock.php
	public function atomicAttach($fileName, $data){
		$fileHandler = fopen($fileName, "a+");
		if(flock($fileHandler, LOCK_EX)){
			fwrite($fileHandler, $data);
			flock($fileHandler, LOCK_UN);
			fclose($fileHandler);
			return True;
		}
		else
		{
			fclose($fileHandler);
			return False;
		}
	}
	public function atomicModifyHtPasswdFile($fileName, $userName, $encryptedPassword)
	{
		$fileHandler = fopen($fileName, "r+");
		if(flock($fileHandler, LOCK_EX)){
			$userExists = False;
			$newFileContent = "";
			$fileContentLines = explode("\n", fread($fileHandler, filesize($fileName)));
			foreach ($fileContentLines as $fileContentLine)
			{
				# Empty line (happens for last line break too):
				if($fileContentLine=="") continue;
				# Parse line in the style of "username:password"
				[$currentUserName, $currentPassword] = explode(':', $fileContentLine);
				# See if user exists already
				if($currentUserName==$userName){
					# User matched line in our user file
					$newFileContent .= $userName . ":" . $encryptedPassword . "\n";
					$userExists = True;
				}
				else{
					# User did not match
					$newFileContent .= $fileContentLine . "\n";
				}

			}
			if($userExists == True){
				file_put_contents($fileName, $newFileContent);
			}

			flock($fileHandler, LOCK_UN);
			fclose($fileHandler);
			if($userExists == False){
				return $this->atomicAttach($fileName, $userName . ":" . $encryptedPassword . "\n");
			}
			return True;
		}
		else
		{
			fclose($fileHandler);
			return False;
		}

	}
}
?>
