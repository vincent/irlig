<?php

class Zend_View_Helper_Js {

	public function js($location) {
		return '<script type="text/ecmascript" xlink:href="http://local.irlig.com/resources/javascript'.$location.'"></script>';
	}

}

?>