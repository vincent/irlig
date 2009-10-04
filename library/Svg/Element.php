<?php

class Svg_Element {

	static $directories = array(
		'character'	=>	'characters',
		'map'		=>	'maps',
		'map_element'=>	'map_elements'
	);

	public static function exists($type, $name) {
		$directory = self::$directories[$type];
		$mapFile = dirname(__FILE__) . '/../../public/resources/'.$directory.'/' . $name . '.svg';
		return file_exists($mapFile);
	}

	public static function get_all($type, $name, $format = 'names', $path = 'g') {

		$return_bag = ($format == 'names' ? array() : '');

		$types = array( $type );

		if ($type == 'all') {
			$types = scandir(dirname(__FILE__) . '/../../public/resources');
			foreach ($types as $k => $t) {
				$types[$k] = array_search($t, self::$directories);
				if (empty($types[$k])) unset($types[$k]);
			}
		}

		foreach ($types as $type) {
			if ($type == '.' OR $type == '..') continue;

			$directory = dirname(__FILE__) . '/../../public/resources/'.self::$directories[$type];
			$names = array( $name );
			if ($name == 'all') {
				$names = scandir($directory);
				foreach ($names as $k => $n) {
					if (preg_match('/\.svg[z]*$/', $n))
						$names[$k] = preg_replace('/\.svg[z]*$/', '', $n);
					else
						unset($names[$k]);
				}
			}

			foreach ($names as $n) {
				if ($format == 'names')
					$return_bag[] = array( 'type'=>$type, 'name'=>$n );
				else {
					$data = self::get($n, $type, $format);

					$data = $data->{$path}->asXml();

					$return_bag .= $data;
				}
			}
		}

		return $return_bag;
	}

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