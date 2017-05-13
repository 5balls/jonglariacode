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
		$fileHandler = fopen($fileName, "rw");
		if(flock($fileHandler, LOCK_EX)){
			$userExists = False;
			$newFileContent = "";
			$fileContentLines = explode("\n", fread($fileHandler, filesize($fileName)));
			foreach ($fileContentLines as $fileContentLine)
			{
				echo "fileContentLine: \"" . $fileContentLine . "\"\n";
				[$currentUserName, $currentPassword] = explode(':', $fileContentLine);
				echo "User: \"" . $currentUserName . "\"\n";
				echo "Password: \"" . $currentPassword . "\"\n";
				if($currentUserName==$userName){
					$newFileContent .= $userName . ":" . $encryptedPassword . "\n";
					$userExists = True;
				}
				else{
					$newFileContent .= $fileContentLine;
				}

			}
			if($userExists == True){
				fwrite($fileHandler, $newFileContent);
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
