<?php

class MapeditController extends Lib_Controller_Abstract
{

    public function init()
    {
        /* Initialize action controller here */

    }


    public function indexAction()
    {
		$this->_helper->layout->setLayout('mapedit');

		$map = $this->_request->getParam('map');
		if (!$map) return;

		$this->view->mapName = $map;
		$this->view->jsMatrix = json_encode(Svg_Map::createMapAdjacencyMatrix($map));

		$this->view->mode = 'edit';
    }

	public function updatecellAction()
	{
		$this->_helper->layout->setLayout('dummy');

		$map = $this->_request->getParam('map');
		if (!$map) return;

		$attributes = array_intersect_key(
						$this->_request->getParams(),
						array(
							'class' => 1
						)
					);

		$cell_id = $this->_request->getParam('cell_id');
		$cell_index = intval(str_replace('cell_', '', $cell_id));

		$mapData = Svg_Map::getMap($map);

		/*
		//print_r($mapData);

		print_r($mapData->xpath('/polygon'));

		var_dump($mapData->xpath('//polygon[@id = "'.$cell_id.'"]'));

		print_r($mapData->xpath('id('.$cell_id.')'));

		print_r($mapData->polygon[$cell_index]);
		die;

		foreach ($mapData->polygon as $poly) {
			//if ((string)$poly->attributes['id'] == $cell_id)
				$a = get_object_vars($poly);
				reset($a);
				var_dump($a);
		}

		die;

		$mapData = $mapData->xpath('id('.$cell_id.')');


		foreach ($mapData->xpath->children() as $poly) {
			if ((string)$poly->attributes['id'] == $cell_id) {
				error_log('Found cell');
				foreach ($attributes as $k => $v)
					(string)$poly->attributes[$k] = $v;
				break;
			}
		}
		*/

		$mapData->g->polygon[$cell_index]['class'] = $attributes['class'];

		Svg_Map::saveMap($map, $mapData);

		$this->view->data = true;
	}



    public function postDispatch()
	{
		header('Content-Type: application/xml;');
	}


}


