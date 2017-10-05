

Magento Module Tool Documentation

Created by Dwaine A. Hinds.
This application is free for all to use/modify.
This application may not be sold.


Requirements:
1) PHPUNIT
2) php
3) Webserver
4) composer(autoloader)


What is Magento Module Tool?
This is a application that will create,unit test and install custom modules into magento. This allows you to isolate and focus just on developing and testing the module.



Why Magento Module Tool?
Magento is a frustratingly complex platform at times. I have read many pages on how to build custom modules and this application is the
 result. This application serves as a helper to avoid common mistakes and speed up the module creation process.


How does this works?
Templates are used extensively by Magento Module Tool create a generalized dummy module that you customize and unit test.
Once a module is generated it is placed in the output folder for further testing and development. 


How to use?
1) Place the module tool folder in the same parent folder as magento e.g. If magento is installed in ../somedir/magento then module tool
   must placed at ../somedir/moduletool.
   
2) Navigate to the url of the web page "start_here.html"   

3) Fillout the details of your custom module and submit. A skeleton module is created immediately on submit and placed in the 
   "output" folder.

4) Launch command prompt via batch file, usually this is done automatically.

5) Test and ensure there are NO errors or failures. 

6) When there are no errors or failures, then the user will be given the
   option of installing the module into magento.


NB
---
This application was only tested on magento 1.7.2 and php7


