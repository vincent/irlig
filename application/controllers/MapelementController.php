<?php

class MapelementController extends Lib_Controller_Abstract
{

    public function init()
    {
        /* Initialize action controller here */

    }


    public function indexAction()
    {
    }

    public function getAction($element = '')
    {
		//$this->_helper->layout->disableLayout();

		$type = $this->_request->getParam('sub0');
		$name = $this->_request->getParam('sub1');

		if ($type == 'all' OR $name == 'all') {
			$this->view->elements = Svg_Element::get_all($type, $name);
		}
		else {
			if (Svg_Element::exists($type, $name))
				$this->view->elements = array( array( 'type'=>$type, 'name'=>$name ) );
		}
    }

    public function postDispatch()
	{
		header('Content-Type: application/xml;');
	}


}


