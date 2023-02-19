<?php
# Class for atomic file handling (should be used only where really
# needed)
namespace Jonglaria;

class AtomicFile
{
	# From http://php.net/manual/en/function.flock.php
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
	private function atomicAction($fileName, $callbackClass, $callbackUnderLock, $callBackArgs, $fileOpenType)
	{
		$fileHandler = fopen($fileName, $fileOpenType);
		if(flock($fileHandler, LOCK_EX))
		{
			$retval = call_user_func_array(array($callbackClass, $callbackUnderLock), array($fileHandler, $fileName, $callBackArgs));

			flock($fileHandler, LOCK_UN);
			fclose($fileHandler);
			return array(True, $retval);
		}
		else
		{
			fclose($fileHandler);
			return array(False);
		}
	}
	public function atomicReadAction($fileName, $callbackClass, $callbackUnderLock, $callBackArgs){
		return $this->atomicAction($fileName, $callbackClass, $callbackUnderLock, $callBackArgs, "r");
	}
	public function atomicWriteAction($fileName, $callbackClass, $callbackUnderLock, $callBackArgs){
		return $this->atomicAction($fileName, $callbackClass, $callbackUnderLock, $callBackArgs, "w");
	}
	public function atomicReadWriteAction($fileName, $callbackClass, $callbackUnderLock, $callBackArgs){
		return $this->atomicAction($fileName, $callbackClass, $callbackUnderLock, $callBackArgs, "r+");
	}
	public function atomicAppendAction($fileName, $callbackClass, $callbackUnderLock, $callBackArgs){
		return $this->atomicAction($fileName, $callbackClass, $callbackUnderLock, $callBackArgs, "a");
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
				$currentLineElement = explode(':', $fileContentLine);
                                $currentUserName = $currentLineElement[0];
                                $currentPassword = $currentLineElement[1];
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
