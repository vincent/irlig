<?php

class IndexController extends Lib_Controller_Abstract
{

    public function init()
    {
        /* Initialize action controller here */
		$this->_helper->layout->setLayout('index');
    }


    public function indexAction()
    {

		/*
		if ($this->_request->getParam('login')) {
			$_SESSION['login'] = $this->_request->getParam('login');
			$this->_forward( 'index', 'game', null);die;
		}
		*/

		$this->view->maps = Svg_Element::get_all('map', 'all');
    }



}