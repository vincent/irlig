<?php

class Zend_View_Helper_Element {

	public function element($name, $type, $path = 'g') {

		$data = Svg_Element::get($name, $type);

		return $data->{$path}->asXML();
	}

}

?>