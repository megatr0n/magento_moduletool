<?php
//this script just observes the event in magento. Then saves what was observed.

class companyname_modulename_Model_Observer
{
    public function action_method_name(Varien_Event_Observer $observer)
    {
	$current_script_path = realpath(dirname(__FILE__)).'/';		
	//require_once($current_script_path.'../../common_code.php');
	$datacollection_arr = array();
	$datacollection_arr = $observer->getEvent()->Datacollection_key()
        if ($validity == 'true') 
        {
        require_once($current_script_path.'action_method_name.php');
        }	
		else
		{
			
		}            
    return $this;
    }

}

?>