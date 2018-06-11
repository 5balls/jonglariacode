<?php
namespace Jonglaria;

class Form
{
    # Data from the web may be dangerous, so we only allow access
    # to unsanitized data to this class (and not its children):
    private $formdata = array();
    protected $sanitized_formdata = array();
    protected $valid_formdata = array();
    protected $validstring_formdata = array();
    protected $storagestring_formdata = array();
    public function __construct(array $fd){
	# In general, we try to find functions for the input
	# data and if they don't exist, we try to do something
	# reasonable.
	$this->formdata = $fd;
	foreach($this->formdata as $inputkey => $inputval){

	    # Sanitization:
	    $san_fun = "sanitize_" . $inputkey;
	    if(is_callable(array($this, $san_fun))){
		$this->sanitized_formdata[$inputkey] = call_user_func_array(array($this,$san_fun),array($inputval));
	    }
	    else{
		$this->sanitized_formdata[$inputkey] = $this->sanitize_generic($inputval);
	    }
	    # From now on only work on sanitized input:
	    $sinputval = $this->sanitized_formdata[$inputkey];

	    # Validation
	    $val_fun = "validate_" . $inputkey;
	    if(is_callable(array($this, $val_fun))){
		$this->valid_formdata[$inputkey] = call_user_func_array(array($this,$val_fun),array($sinputval));
		# Only fill validation string in case
		# of validation failure:
		if($this->valid_formdata[$inputkey] !== true){
		    $valstr_fun = "validationstring_" . $inputkey;
		    if(is_callable(array($this, $valstr_fun))){
			$this->validstring_formdata[$inputkey] = call_user_func_array(array($this,$valstr_fun),array($sinputval));
		    }
		}
	    }
	    else{
		# This is debatable, but for
		# convenience right now we assume,
		# that if no function is defined for
		# validation we don't neeed to
		# validate.
		$this->valid_formdata[$inputkey] = true;
	    }

	    # Storage string
	    $store_fun = "store_" . $inputkey;
	    if(is_callable(array($this, $store_fun))){
		$this->storagestring_formdata[$inputkey] = call_user_func_array(array($this,$store_fun),array($sinputval));
	    }
	    else{
		$this->storagestring_formdata[$inputkey] = json_encode(array($inputkey => $sinputval));
	    }


	}

    }
    private function sanitize_generic($inputval){
	if(is_array($inputval)){
	    $ret_array = array();
	    foreach($inputval as $inputval_key => $inputval_elem){
		$ret_array[$inputval_key] = htmlentities($inputval_elem, ENT_QUOTES , "UTF-8");
	    }
	    return $ret_array;
	}
	else{
	    return htmlentities($inputval, ENT_QUOTES , "UTF-8");
	}
    }
    public function get_vals(){
	return array($this->sanitized_formdata,$this->valid_formdata,$this->validstring_formdata,$this->storagestring_formdata);
    }

    public function get_vals_stridx(){
	return array(
		"sanitized" => $this->sanitized_formdata,
		"valid" => $this->valid_formdata,
		"validstr" => $this->validstring_formdata,
		"storage" => $this->storagestring_formdata);

    }
    # This is just for convenience:
    public function is_valid($subarray){
	if(is_array($subarray)){
	    foreach($subarray as $key => $val){
		if($this->is_valid($val) === false){
		    return false;
		}
	    }
	    return true;
	}
	else{
	    if($subarray == false){
		return false;
	    }
	    else{
		return true;
	    }
	}
#	return ! in_array(false, $this->valid_formdata);
    }
}
?>
