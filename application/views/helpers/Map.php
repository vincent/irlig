<?php

class Zend_View_Helper_Map {

	public function map($mapName) {

		$mapData = Svg_Map::getMap($mapName);

		return $mapData->g->asXML();
	}

}

?>