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

	public function edit()
	{
		$this->_helper->layout->setLayout('dummy');

		$map = $this->_request->getParam('map');
		if (!$map) return;

		switch ($this->_request->getParam('map_action')) {

			case 'set_cell_type':
				$cell_id = $this->_request->getParam('cell_id');
				$cell_type = $this->_request->getParam('type');
				if (empty($cell_id) OR !$cell_type) return;

				$mapData = Svg_Map::getMap($map);
				foreach ($mapData->g->children() as $poly) {
					if ((string)$poly->attributes['id'] == $cell_id) {
						(string)$poly->attributes['type'] = $cell_type;
						break;
					}
				}

				Svg_Map::saveMap($map, $mapData);
			break;
		}

	}


    public function postDispatch()
	{
		header('Content-Type: application/xml;');
	}


}


