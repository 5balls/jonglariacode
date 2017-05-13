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
}
?>
