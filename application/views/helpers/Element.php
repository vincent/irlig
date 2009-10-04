<?php

class Zend_View_Helper_Element {

	public function element($name, $type, $path = 'g') {

		if ($name == 'all') {
			$data = Svg_Element::get_all($type, $name, 'xml');
			return $data;
		}
		else {
			$data = Svg_Element::get($name, $type, 'xml');
			return $data->{$path}->asXML();
		}
	}

}

?>