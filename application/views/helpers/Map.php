<?php

class Zend_View_Helper_Map {

	public function map($mapName) {

		$mapData = Svg_Map::getMap($mapName);

		$mapData = $mapData->asXML();
		$mapData = preg_replace('/<\?xml [^\<]*\?>/', '', $mapData);

		return $mapData;
	}

}

?>