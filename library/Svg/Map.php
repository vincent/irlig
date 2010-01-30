<?php

class Svg_Map {

	public static function createMapIndex($mapName) {
		$points = array();

		$xml = self::getMap($mapName, 'xml');

		foreach($xml->g->path as $child) {

			$attrs = $child->attributes();

			foreach (array('NW', 'N', 'NE', 'E', 'SE', 'S', 'SW', 'W') as $direction)
				if (isset($attrs['cell_'.$direction]))
					$points[] = array( (string) $attrs['id'], (string) $attrs['cell_'.$direction], 1);
		}

		$mapFile = dirname(__FILE__) . '/../../public/resources/maps/' . $mapName . '.svg';
		file_put_contents($mapFile.'.idx', serialize($points));

		return $points;
	}

	public static function createMapAdjacencyMatrix($mapName) {
		$map = self::getMap($mapName, 'xml');

		$maproot = $map->g->attributes();
		$maproot = array(
			'rows'	=>	(string) $maproot['rows'],
			'columns'	=>	(string) $maproot['columns']
		);

		//$matrix = array_fill( 0, $maproot['rows'], array_fill( 0, $maproot['columns'], null ) );
		$matrix = array();

		$cell = 0;
		for ($r = 0; $r < $maproot['rows']; $r++) {
			for ($c = 0; $c < $maproot['columns']; $c++) {
				$matrix[$r][$c] = array(
					'id'	=>	'cell_'.$cell,
					'pos'	=>	array( 'x'=>$r, 'y'=>$c ),
				);
				/**/
				//$matrix['cell_'.$cell] = array( 'x'=>$r, 'y'=>$c );

				$cell++;
			}
		}

		return $matrix;
	}

	public static function getMapIndex($mapName) {
		$mapFile = dirname(__FILE__) . '/../../public/resources/maps/' . $mapName . '.svg';

		if (!file_exists($mapFile.'.idx'))
			$points = self::createMapIndex($mapName);
		else
			$points = unserialize(file_get_contents($mapFile.'.idx'));

		return $points;
	}

	public static function getMap($mapName, $format = 'xml') {
		$mapFile = dirname(__FILE__) . '/../../public/resources/maps/' . $mapName . '.svg';

		switch ($format) {

			case 'xml':
				return simplexml_load_file($mapFile);

			default:
			case 'plain':
				return file_get_contents($mapFile);
		}
	}


	public static function saveMap($mapName, $mapData) {
		$mapFile = dirname(__FILE__) . '/../../public/resources/maps/' . $mapName . '.svg';

		file_put_contents($mapFile, $mapData->asXml());
	}
}