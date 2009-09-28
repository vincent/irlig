<?php 
// ** Check to see if the environment is already setup **
if (isset($bootstrap) && $bootstrap) { 
    require_once('common.fn.php');
    
    // Enable all errors so we'll know when something goes wrong. 
    ini_set('display_errors', 'stdout');
    ini_set('display_startup_errors', 'stdout');  
    ini_set('error_reporting', E_ALL | E_STRICT);
 
    // Add our {{library}} directory to the include path so that PHP can find the Zend Framework classes.
    // you may wish to add other paths here, or keep system paths: set_include_path('../library' . PATH_SEPARATOR . get_include_path() 
    set_include_path('../library');  
 
    // Set up autoload. 
    // This is a nifty trick that allows ZF to load classes automatically so that you don't have to litter your 
    // code with 'include' or 'require' statements. 
    require_once "Zend/Loader.php"; 
    Zend_Loader::registerAutoload(); 
} 
 
# build the view correctly
$view = new Zend_View();
$view->setEncoding('UTF-8')
	->setScriptPath('../application/views/scripts/')
	->addHelperPath('../application/views/helpers/')
	->addScriptPath('../application/views/partials/');
	
$layoutType = !empty($_REQUEST['layout']) ? $_REQUEST['layout'] : 'default';
$layout = Zend_Layout::startMvc();
$layout->setLayout($layoutType)
	->setView($view)
	->setLayoutPath('../application/views/layouts/')
	->setViewSuffix('phtml');


# modify the viewRenderer default behaviour
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
$viewRenderer->setView($view);

// ** Get the front controller ** 
// The Zend_Front_Controller class implements the Singleton pattern, which is a design pattern used to ensure 
// there is only one instance of Zend_Front_Controller created on each request. 
$frontController = Zend_Controller_Front::getInstance(); 
 
// Point the front controller to your action controller directory. 
$frontController->setControllerDirectory('../application/controllers'); 
 
// Set the current environment 
// Set a variable in the front controller indicating the current environment -- 
// commonly one of development, staging, testing, production, but wholly 
// dependent on your organization and site's needs. 
$frontController->setParam('env', 'development');
$frontController->throwExceptions(true);
        
