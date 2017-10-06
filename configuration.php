<?php   

//the purpose of this file is to write the correct settings to the template files

    $autoloadfile = $_POST['autoloadfile'];// this is composer's autoloader file location e.g. "../../../vendor/autoload.php";
    $targeted_method = $_POST['targeted_method'];//this is the observer object's method.
	$event_method = $_POST['event_method'];//this is the action method that will be triggered when event occurs.
	$returnval = $_POST['returnval'];//this can be any data type integer, float...
    $targeted_method_arguments_str = $_POST['targeted_method_arguments_str'];
	$targeted_method_arguments_arr = array($targeted_method_arguments_str); //these are methods that can be used for the model you get from the observer e.g.(chained) $Observer->model_method1->model_method2->model_method3;
	$company_name = $_POST['company_name'];
	$modulename = $_POST['modulename'];
	
	$my_custom_events_str = $_POST['my_custom_events_str'];
	$my_custom_events_arr = array();
	$my_custom_events_arr = explode(",",$my_custom_events_str);//these are the custom events
	
	$dispatched_events_files_str = $_POST['dispatched_events_files_str'];//, file2,
	$dispatched_events_files_arr = array();
	$dispatched_events_files_arr = explode(",",$dispatched_events_files_str);//this is file that you intend to or have used the disptachEvent code in.
    
	$magento_dir = $_POST['magento_dir'];// magento installation directory
	process_settings($company_name,$modulename,$targeted_method,$event_method,$targeted_method_arguments_arr,$my_custom_events_arr,$returnval,$magento_dir,$dispatched_events_files_arr,$autoloadfile);

	function getcurrentdirurl()
	{
	$current_script_path = realpath(dirname(__FILE__)).'\\';
	$htdoc_pos = stripos($current_script_path,'htdocs');
    $apath = substr($current_script_path,$htdoc_pos+7,PHP_INT_MAX);	
	$apath = str_ireplace("\\","/",$apath);
    $thebaseurl = 'http://'.$_SERVER['HTTP_HOST'].'/';
	$thedirurl = $thebaseurl.$apath;
	return $thedirurl;
	}
		
   function getinnertext($ftoken,$stoken,$intext)
   {
   //echo "<br /><br />before cleaning =".$intext;
   $copytext = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $intext);
   //echo "<br /><br />after cleaning =".$copytext;
   $copytext = strtolower($copytext);	

      //find first token pos
        if(stripos($copytext,$ftoken) != FALSE)
        { 	  
         // copy substring starting from first token to end into new string.
        $fpos = stripos($copytext,$ftoken);
        // echo "<br />fpos =".$fpos;
        $copytext = substr($copytext,($fpos+strlen($ftoken))); 
   
        //find second token pos in new string
        $spos = stripos($copytext,$stoken);
        //echo "<br />spos =".$spos;    
           if ($fpos!=FALSE && $spos!=FALSE)
           {
		   //copy substring starting from beginning upto second token pos into new string	
            $copytext = substr($copytext,0,$spos);
           } 
        //echo "<br />new copytext -->".$copytext."<--";  
        }
    return $copytext;	
    }  
	
   function process_settings($company_name,$modulename,$targeted_method,$event_method,$targeted_method_arguments_arr,$my_custom_events_arr,$returnval,$magento_dir,$dispatched_events_files_arr,$autoloadfile)
   {	
    $current_script_path = realpath(dirname(__FILE__)).'\\';
    //this section we are converting the first letter to uppercase
	$company_name = strtolower($company_name);
	$fl = $company_name[0];
    $fl = strtoupper($fl);
	$company_name = substr($company_name,1,PHP_INT_MAX);
    $company_name = $fl.$company_name; 	

	$modulename = strtolower($modulename);
	$fl = $modulename[0];
    $fl = strtoupper($fl);	
    $modulename = substr($modulename,1,PHP_INT_MAX);
    $modulename = $fl.$modulename; 
	
	$fullname = $company_name."_".$modulename;
	$observerfile = $current_script_path.'output\\local\\'.$company_name.'\\'.$modulename.'\\Model\\Observer.php';
    $eventmethodfile = $current_script_path.'output\\local\\'.$company_name.'\\'.$modulename.'\\Model\\'.$event_method.'.php';
   
    //preparing module xml file
    $modulexmlfile = $current_script_path.'output\\'.$fullname.'.xml';
	$txtstr = file_get_contents($current_script_path.'templates\\companyname_modulename.xml');	
	$txtstr = str_ireplace('companyname_modulename',$fullname,$txtstr);
	//write data to file
		if(file_exists($modulexmlfile))
	    {
		unlink($modulexmlfile);   
	    }
	file_put_contents($modulexmlfile,$txtstr);	
	

	//preparing events for config.xml file	
	$configxmlfile = $current_script_path.'output\\local\\'.$company_name.'\\'.$modulename.'\\etc\\config.xml';

	
	$eventsxml = "";
	$events = "";
	$partial_dispatchEvents_codes = "";
	$dispatcherfiles = "";
	$dispatchcodes = "";
	
    //generate dispatch codes
	$dispatchcodefile = $current_script_path.'dispatchcodes.txt';
	$numev = count($my_custom_events_arr);	
        for($ev=0; $ev < $numev; $ev++)
	    {			
		//here we will replace the events tokens
	    $txtstr = file_get_contents($current_script_path.'templates\\events.xml');		
        $txtstr = str_ireplace('event_name',$my_custom_events_arr[$ev],$txtstr);
		$eventsxml = $eventsxml.$txtstr.',';
        $events = $events.'"'.$my_custom_events_arr[$ev].'",';	
		$disp = "dispatchEvent('".$my_custom_events_arr[$ev]."'";
		$partial_dispatchEvents_codes = $partial_dispatchEvents_codes.'"'.$disp.'",';
		
		//assemble codes
		$txtstr = file_get_contents($current_script_path.'templates\\dispatch.txt');
	    $txtstr = str_ireplace('custom_event',$my_custom_events_arr[$ev],$txtstr);
	    $dispatchcodes = $dispatchcodes.$txtstr;	
	    }	
	    //write data to file
	    if(file_exists($dispatchcodefile))
	    {
		unlink($dispatchcodefile);   
	    }		
	file_put_contents($dispatchcodefile,$dispatchcodes);	


	//preparing dispatcher files
	$numfiles = count($dispatched_events_files_arr);	
        for($dfile=0; $dfile < $numfiles; $dfile++)
	    {		
		$dispatcherfiles = $dispatcherfiles.'"'.$dispatched_events_files_arr[$dfile].'",';
		} 

	$charcnt = strlen($dispatcherfiles);	
	$dispatcherfiles = substr($dispatcherfiles,0,$charcnt-1);		
		
	$charcnt = strlen($events);	
	$events = substr($events,0,$charcnt-1);		
	
	$charcnt = strlen($partial_dispatchEvents_codes);	
	$partial_dispatchEvents_codes = substr($partial_dispatchEvents_codes,0,$charcnt-1);	
	
    $txtstr = file_get_contents($current_script_path.'templates\\companyname_modulename_config.xml');		
	
	//here we will replace the event token in config.xml
    $txtstr = str_ireplace('<events>','<events>'.$eventsxml,$txtstr);	
	
	//here we attach the action method that will be triggered 
	$txtstr = str_ireplace('action_method_name',$event_method,$txtstr);	
	
		//write data to file
	    if(!file_exists($current_script_path.'output\\local\\'.$company_name.'\\'.$modulename.'\\etc\\'))
	    {
	    mkdir($current_script_path.'output\\local\\'.$company_name.'\\'.$modulename.'\\etc\\', 0777, true);
		}
	$txtstr = str_ireplace('companyname_modulename',$fullname,$txtstr);	
	    if(file_exists($configxmlfile))
	    {
		unlink($configxmlfile);   
	    }
	file_put_contents($configxmlfile,$txtstr);	

		
			
	//preparing fakeclass.php file	
	$arguments = "";
		$numarg = count($targeted_method_arguments_arr);
        for($ag=0; $ag < $numarg; $ag++)
	    {
		$arguments = $arguments.'"'.$targeted_method_arguments_arr[$ag].'",';	
	    }
	$charcnt = strlen($arguments);	
	$arguments = substr($arguments,0,$charcnt-1);	
    $txtstr = file_get_contents($current_script_path.'templates\\fakeclass.php');		
	
	//set return value by replacing returnvalue token
    $txtstr = str_ireplace('retval','"'.$returnval.'"',$txtstr); 

	//write data to file
	file_put_contents($current_script_path.'fakeclass.php',$txtstr);

 
	//preparing to insert newcode into main_tester.php
	$testcode = file_get_contents($current_script_path.'templates\\tests.txt'); 
	$configxmlfile = str_ireplace("\\","/",$configxmlfile);
	$observerfile = str_ireplace("\\","/",$observerfile);
	$modulexmlfile = str_ireplace("\\","/",$modulexmlfile);
	$eventmethodfile = str_ireplace("\\","/",$eventmethodfile);
	$event_method_file_url = getcurrentdirurl().'output/local/'.$company_name.'/'.$modulename.'/Model/'.$event_method.'.php';
	
	$private_variables = '	
	private $mock_model = "";//$mock_model = $mock_observer->'.$targeted_method.'('.$arguments.');	
	private $expectedresult = "";	
	private $theresult = "";
	private $fnd = false;
	private $company_name = "'.$company_name.'";
	private $modulename = "'.$modulename.'";
	private $configxmlfile = "'.$configxmlfile.'";
	private $observerfile = "'.$observerfile.'";
	private $modulexmlfile = "'.$modulexmlfile.'";
	private $eventmethodfile = "'.$eventmethodfile.'";
	private $event_method = "'.$event_method.'";
	private $action_events_arr = array('.$events.');
	private $partial_dispatchEvents_codes_arr = array('.$partial_dispatchEvents_codes.');
	private $dispatched_events_files_arr = array('.$dispatcherfiles.');
	private $theurl = "'.$event_method_file_url.'";
	
	//these are variables used like function arguments/parameters. Values are assigned before use.
	private $node = "";
	private $partial_dispatchEvent_code = "";
	private $filelocation = "";
	private $correctcount = "";
	';
	
	$newcode = '   
	//test config.xml has valid xml DOM
	$this->filelocation = "'.$configxmlfile.'"; 
	$this->xmlvalidity();
    //amocktestclass::xmlvalidity();
	
	//test if module xml file has valid xml DOM
	$this->filelocation = "'.$modulexmlfile.'"; 
	$this->xmlvalidity();

    //test for observer file integrity for event method
    $this->node = $this->event_method;
    $this->filelocation = $this->observerfile;
    $this->correctcount = 2;
	$this->fileintegrity();

    //test for observer file integrity for correct class name.
	$this->node = "'.$fullname.'_Model_Observer";
    $this->filelocation = $this->observerfile;
    $this->correctcount = 1;
	$this->fileintegrity();
		
	//test module xml file integrity for correct nodes
	$this->node = $this->company_name."_".$this->modulename;
    $this->filelocation = $this->modulexmlfile;
    $this->correctcount = 2;
    $this->fileintegrity();
	
	//test config.xml file integrity for correct fullname nodes
	$this->node = $this->company_name."_".$this->modulename;
    $this->filelocation = $this->configxmlfile;
    $this->correctcount = 5+(3*count($this->action_events_arr));
    $this->fileintegrity();	
	
	//test config.xml file integrity for correct event method nodes
	$this->node = $this->event_method;
    $this->filelocation = $this->configxmlfile;
    $this->correctcount = count($this->action_events_arr);	
    $this->fileintegrity();	
	
	//test config.xml file integrity for correct events name nodes	
	    foreach ($this->action_events_arr as $event) 
		{
		$this->node = $event;
        $this->filelocation = $this->configxmlfile;
        $this->correctcount = 2;		
		$this->fileintegrity();		
        }
		
	//test dispatchEvent file for correct dispatchEvents codes	
	    foreach ($this->partial_dispatchEvents_codes_arr as $dispatchEvent) 
		{
			foreach ($this->dispatched_events_files_arr as $dispatchfile)
			{				
		    $this->partial_dispatchEvent_code = $dispatchEvent;
            $this->filelocation = $dispatchfile;
		    $this->fileforDispatchEvent();				
			}
		
        }
	$this->assertTrue($this->fnd);
	    if($this->fnd == false){
		echo "none of the dispatchEvents codes are used in any one of the files suggested.";	
		}
	
	
	//test all the essential file
	$this->ifEssentialfilesExists($this->company_name,$this->modulename,$this->event_method);

	 //test event action script file result
	$this->eventactionscriptfileresult($this->theurl);

	echo "theresult ".$this->theresult;
    $this->assertEquals($this->expectedresult, $this->theresult);	
	';
	    if(file_exists($current_script_path.'main_tester.php'))
	    {
		$txtstr = file_get_contents($current_script_path.'main_tester.php');   
		$currentcode = getinnertext('//<----- do not edit above here --->','//<----- do not edit below here --->',$txtstr);
	    $txtstr = str_ireplace('//<----- do not edit above here --->'.$currentcode.'//<----- do not edit below here --->','//<----- do not edit above here --->'.$newcode.'//<----- do not edit below here --->',$txtstr);
		echo "<br /><h3>the old code from main_tester.php will be retained. If you do not want this, just delete the current main_tester.php file.</h3>";
		}   
        else
		{
		$txtstr = file_get_contents($current_script_path.'templates\\main_tester.php'); 	
		$txtstr = str_ireplace('//<----- do not edit above here --->','//<----- do not edit above here --->'.$newcode,$txtstr);
		}	
   	$txtstr = str_ireplace('arguments',$arguments,$txtstr);
	$txtstr = str_ireplace('tests_token',$testcode,$txtstr);
	$txtstr = str_ireplace('retval','"'.$returnval.'"',$txtstr);
	$txtstr = str_ireplace('private_token',$private_variables,$txtstr);
	
	//insert the autoload file location
	$txtstr = str_ireplace('autoloadfile',$autoloadfile,$txtstr); 	 
	
	//write data to file
	file_put_contents($current_script_path.'main_tester.php',$txtstr);
	

    //preparing Observer.php file
    $observerfile = $current_script_path.'output\\local\\'.$company_name.'\\'.$modulename.'\\Model\\'.'Observer.php';
	$event_method = strtolower($event_method);
	$txtstr = file_get_contents($current_script_path.'templates\\Observer.php');
	$txtstr = str_ireplace('companyname_modulename',$fullname,$txtstr); 
	$txtstr = str_ireplace('action_method_name',$event_method,$txtstr); 
	//write data to file
	    if(!file_exists($current_script_path.'output\\local\\'.$company_name.'\\'.$modulename.'\\Model\\'))
	    {
	    mkdir($current_script_path.'output\\local\\'.$company_name.'\\'.$modulename.'\\Model\\', 0777, true);
		}		
	    if(file_exists($observerfile))
	    {
		unlink($observerfile);   
	    }	
	file_put_contents($observerfile,$txtstr);	
	
    //preparing event action script file
    	
	$event_method = strtolower($event_method);
	$txtstr = '<?php $output_result = 1+3; echo $output_result; ?>';
	//write data to file
		if(!file_exists($current_script_path.'output\\local\\'.$company_name.'\\'.$modulename.'\\Model\\'))
	    {
	    mkdir($current_script_path.'output\\local\\'.$company_name.'\\'.$modulename.'\\Model\\', 0777, true);
		}	
	    if(file_exists($eventmethodfile))
	    {
		unlink($eventmethodfile);   
	    }		
	file_put_contents($eventmethodfile,$txtstr);	


	
	
	//preparing batch file
	
	$configxmlfile = str_ireplace("/","\\",$configxmlfile);
	$observerfile = str_ireplace("/","\\",$observerfile);
	$modulexmlfile = str_ireplace("/","\\",$modulexmlfile);
	$eventmethodfile = str_ireplace("/","\\",$eventmethodfile);
	
	$txtstr = file_get_contents($current_script_path.'templates\\runtest.txt');
	$txtstr = str_ireplace('current_directory',getcwd(),$txtstr); 
	$txtstr = str_ireplace('maintestfile','main_tester.php',$txtstr); 
	
	$txtstr = str_ireplace('configxmlfile_here',$configxmlfile,$txtstr);
	$txtstr = str_ireplace('observerfile_here',$observerfile,$txtstr);
	$txtstr = str_ireplace('modulexmlfile_here',$modulexmlfile,$txtstr);
	$txtstr = str_ireplace('eventmethodfile_here',$eventmethodfile,$txtstr);
	
	$txtstr = str_ireplace('companyname_here',$company_name,$txtstr);
	$txtstr = str_ireplace('modulename_here',$modulename,$txtstr);
	$txtstr = str_ireplace('magentodir_here',$magento_dir,$txtstr);
	
	//setup folders
	$txtstr = str_ireplace('%batfilepath%',$current_script_path,$txtstr);
	$txtstr = str_ireplace('%company_name%',$company_name,$txtstr);
	$txtstr = str_ireplace('%modulename%',$modulename,$txtstr);
	$txtstr = str_ireplace('%magentodir%',$magento_dir,$txtstr);
	
	//installation paths
	$txtstr = str_ireplace('configxmlfile_destination',$current_script_path.'..\\'.$magento_dir.'\\app\\code\\local\\'.$company_name.'\\'.$modulename.'\\etc\\config.xml',$txtstr);
	$txtstr = str_ireplace('observerfile_destination',$current_script_path.'..\\'.$magento_dir.'\\app\\code\\local\\'.$company_name.'\\'.$modulename.'\\Model\\Observer.php',$txtstr);
	$txtstr = str_ireplace('modulexmlfile_destination',$current_script_path.'..\\'.$magento_dir.'\\app\\etc\\modules\\'.$fullname.'.xml',$txtstr);
	$txtstr = str_ireplace('eventmethodfile_destination',$current_script_path.'..\\'.$magento_dir.'\\app\\code\\local\\'.$company_name.'\\'.$modulename.'\\Model\\'.$event_method.'.php',$txtstr);

		if(file_exists($current_script_path.'runtest.bat'))
	    {
		unlink($current_script_path.'runtest.bat');   
	    }	
	
    file_put_contents($current_script_path.'runtest.bat',$txtstr);
echo "<h2>module created in output dir.</h2>";	
    //amen	
    }
?>