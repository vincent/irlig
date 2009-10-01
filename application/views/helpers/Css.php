<?php

class Zend_View_Helper_Css {

	public function css($location, $media = 'screen', $type = 'link') {
		switch ($type) {
			case 'link':
			    return '<link href="http://local.irlig.com/resources/css'.$location.'" media="'.$media.'" rel="stylesheet"></link>';
			case 'style':
				$filename = dirname(__FILE__) . '/../../../public/resources/css'.$location;
				if (file_exists($filename))
				    return '<style type="text/css">'.file_get_contents( $filename ).'</style>';
			case 'xml':
				return '<?xml-stylesheet href="http://local.irlig.com/resources/css'.$location.'" type="text/css" ?>';
		}
	}

}

?>