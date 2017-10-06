<?php 

$current_script_path = realpath(dirname(__FILE__)).'\\';
system('cmd /c "'.$current_script_path.'runtest.bat"');

?>