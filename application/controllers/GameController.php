<?php

class GameController extends Lib_Controller_Abstract
{

    public function init()
    {
        /* Initialize action controller here */
		$this->_helper->layout->setLayout('svg');

    }


    public function indexAction()
    {
		$map = $this->_request->getParam('map') ? $this->_request->getParam('map') : 'simple';

		$this->view->mapName = $map;
		$this->view->jsMatrix = json_encode(Svg_Map::createMapAdjacencyMatrix($map));
    }


    public function postDispatch()
	{
		header('Content-Type: application/xml;');
	}


}


