<?php

function __($obj, $ret=false) {
    @ob_flush();
	if ($ret) {
		ob_start();
	}
    if (!extension_loaded("xdebug")) {
    	Zend_Debug::dump($obj);
    } else {
    	var_dump($obj);
    }
    if ($ret) {
    	return ob_get_contents();
    }
}
