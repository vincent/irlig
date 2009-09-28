<?php

class Svg_Element {

	static $directories = array(
		'character'	=>	'characters',
		'map'		=>	'maps',
		'map_element'=>	'map_elements'
	);

	public static function get($name, $type, $format = 'xml') {
		$directory = self::$directories[$type];
		$mapFile = dirname(__FILE__) . '/../../public/resources/'.$directory.'/' . $name . '.svg';

		switch ($format) {

			case 'xml':
				return simplexml_load_file($mapFile);

			default:
			case 'plain':
				return file_get_contents($mapFile);
		}
	}

	public static function saveCharacter($name, $data) {
		$directory = self::$directories[$type];
		$mapFile = dirname(__FILE__) . '/../../public/resources/'.$directory.'/' . $name . '.svg';

		file_put_contents($mapFile, $data->asXml());
	}
}