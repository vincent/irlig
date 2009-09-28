<?php

class Zend_View_Helper_Img {

	public function img($location) {
	    return 'http://' . $_SERVER['HTTP_HOST'] . '/resources/images/'.$location;
	}

}

?>