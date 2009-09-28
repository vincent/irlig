<?php

class Zend_View_Helper_Css {

	public function css($location, $media = 'screen', $type = 'link') {
		switch ($type) {
			case 'link':
			    return '<link href="http://local.irlig.com/resources/css'.$location.'" media="'.$media.'" rel="stylesheet"></link>';
			case 'style':
			    return '<style type="text/css">'.file_get_contents( dirname(__FILE__) . '/../../../public/resources/css'.$location ).'</style>';
			case 'xml':
				return '<?xml-stylesheet href="http://local.irlig.com/resources/css'.$location.'" type="text/css" ?>';
		}
	}

}

?>