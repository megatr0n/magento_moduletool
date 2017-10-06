<?php
//this script just observes the event in magento. Then saves what was observed.

class Daloradius_Deleteaccount_Model_Observer
{
    public function delete_account(Varien_Event_Observer $observer)
    {
	$current_script_path = realpath(dirname(__FILE__)).'/';		
	//require_once($current_script_path.'../../common_code.php');
	$datacollection_arr = array();
	$datacollection_arr = $observer->getEvent()->Datacollection_key()
        if ($validity == 'true') 
        {
        require_once($current_script_path.'delete_account.php');
        }	
		else
		{
			
		}            
    return $this;
    }

}

?>