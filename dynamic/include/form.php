<?php
namespace Jonglaria;

# This class is supposed to be used as base class for other classes.
# Those derived classes should only be used by constructor (to keep
# typing to a minimum) as such:
#
# $data = array(
#         new Data('town', strtotime('2018-09-16')+31*24*60*60, "It's nice", 'town', array('admin'),
#         new Data('country', strtotime('2018-09-16')+31*24*60*60, "It's nicer", 'country', array('admin'),
# );
#
#
# $form = new DerivedForm($_POST, 'mydata', '/data/base/dir', $data, 'nextpage.html');
class Form
{
    # Data from the web may be dangerous, so we only allow access
    # to unsanitized data to this class (and not its children):
    private $formdata = array();
    private $dataname;
    private $storebasename;
    private $storedata = array();
    private $authuser;
    private $filename;
    private $redirection;
    protected $sanitized_formdata = array();
    protected $valid_formdata = array();
    protected $validstring_formdata = array();
    protected $storagestring_formdata = array();
    public function __construct(array $fd, $dn, $sbn, array $sd, $rd, $authuser = null){
	$this->formdata = $fd;
        $this->dataname = $dn;
        $this->storebasename = $sbn;
        $this->storedata = $sd;
        $this->redirection = $rd;
        if(null == $authuser){
            $authuser = $_SERVER['PHP_AUTH_USER'];
        }
        $this->authuser = $authuser;
        $this->pathname = $this->storebasename . DIRECTORY_SEPARATOR . $this->authuser;
        $this->filename = $this->pathname . DIRECTORY_SEPARATOR . $this->dataname . '.json';
        if(is_callable(array($this, 'check_precondition'))){
		if(call_user_func(array($this,'check_precondition'))){
                    $GLOBALS['precondition'] = true;
                }
                else{
                    $GLOBALS['precondition'] = false;
                    return;
                }
        }
        else{
            # We are not asked to check a precondition, so we assume it
            # is ok
            $GLOBALS['precondition'] = true;
        }
        if(!empty($_POST)){
            if(is_callable(array($this, 'check_postcondition'))){
                $GLOBALS['postcondition'] = call_user_func(array($this,'check_postcondition'));
                if($GLOBALS['postcondition'] != ''){
                    return;
                }
            }
            # User wants to store new data:
            $this->evaluateformdata();

            if($this->is_valid($this->valid_formdata)){
                if(is_callable(array($this, 'beforestorage'))){
                    $GLOBALS['beforestorage'] = call_user_func(array($this,'beforestorage'));
                    # Something went wrong, so don't do the redirect and
                    # give webpage a chance to react:
                    if(!$GLOBALS['beforestorage']){
                        $GLOBALS['formcontents'] = $this->sanitized_formdata;
                        $GLOBALS['validinput'] = $this->valid_formdata;
                        $GLOBALS['invalidinputstrings'] = $this->validstring_formdata;
                        return;
                    }
                }
                # Data is valid, we can store it in a file:
                # First let's create the directory:
                $oldumask = umask(0);
                mkdir($this->pathname, 0750, true);
                umask($oldumask);
                $data = new UserData($this->filename, $this->authuser);
                # Replace dataname by data in place and add to UserData:
                foreach($this->storedata as $dindex => $dataset){
                    $this->storedata[$dindex]->dataContent = $this->sanitized_formdata[$dataset->objectName];
                    $data->addDataObjectD($this->storedata[$dindex]);
                }
                $data->storeData();
                if(is_callable(array($this, 'afterstorage'))){
                    $GLOBALS['afterstorage'] = call_user_func(array($this,'afterstorage'));
                    # Something went wrong, so don't do the redirect and
                    # give webpage a chance to react:
                    if(!$GLOBALS['afterstorage']){
                        $GLOBALS['formcontents'] = $this->sanitized_formdata;
                        $GLOBALS['validinput'] = $this->valid_formdata;
                        $GLOBALS['invalidinputstrings'] = $this->validstring_formdata;
                        return;
                    }
                }

                # We are done and can redirect to the next page:
                header('Location: ' . $this->redirection);
                # We call die here to prevent any further output:
                die();
            }
            else{
                # There has been some error in the data input, so we
                # show the page again to the user with some remarks:
                # TODO Variable names should be configurable
                $GLOBALS['formcontents'] = $this->sanitized_formdata;
                $GLOBALS['validinput'] = $this->valid_formdata;
                $GLOBALS['invalidinputstrings'] = $this->validstring_formdata;
                return;
            }
        }
        else{
            # There was no data transmitted, so let's see if we have
            # already stored data in a file:
            if(file_exists($this->filename)){
                $data = new UserData($this->filename, $this->authuser);
                $data->fillFromFile();
                $content = array();
                foreach($this->storedata as $dindex => $dataset){
                    $content[$dataset->dataContent] = $data->getDataObject($dataset->dataContent)->dataContent;
                }
                $GLOBALS['formcontents'] = $content;
                return;
            }
            else{
                # No file, no data transmitted, we don't do anything:
                return;
            }

        }


    }
    private function evaluateformdata(){
	# In general, we try to find functions for the input
	# data and if they don't exist, we try to do something
	# reasonable.
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
        # Checkboxes need to be treated specially, as there is only
        # data transmitted, if an option is checked. The user defined
        # function "checkboxes_checked" returns an array of those
        # checkboxes which are expected to be checked:
        if(is_callable(array($this, 'checkboxes_checked'))){
            $checkboxes = call_user_func(array($this,'checkboxes_checked'));
            foreach($checkboxes as $checkbox){
                $this->valid_formdata[$checkbox] = isset($this->sanitized_formdata[$checkbox]);
                # Only fill validation string in case
                # of validation failure:
                if($this->valid_formdata[$checkbox] !== true){
                    $valstr_fun = "validationstring_" . $checkbox;
                    if(is_callable(array($this, $valstr_fun))){
                        $this->validstring_formdata[$checkbox] = call_user_func(array($this,$valstr_fun));
                    }
                }
            }
        }
        # Same for checkboxes expected to be not checked
        if(is_callable(array($this, 'checkboxes_unchecked'))){
            $checkboxes = call_user_func(array($this,'checkboxes_unchecked'));
            foreach($checkboxes as $checkbox){
                $this->valid_formdata[$checkbox] = !isset($this->sanitized_formdata[$checkbox]);
                # Only fill validation string in case
                # of validation failure:
                if($this->valid_formdata[$checkbox] !== true){
                    $valstr_fun = "validationstring_" . $checkbox;
                    if(is_callable(array($this, $valstr_fun))){
                        $this->validstring_formdata[$checkbox] = call_user_func(array($this,$valstr_fun));
                    }
                }
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
