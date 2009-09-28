<?php

class IndexController extends Lib_Controller_Abstract
{

    public function init()
    {
        /* Initialize action controller here */
    }


    public function indexAction()
    {
		if ($this->_request->getParam('login')) {

			$_SESSION['login'] = $this->_request->getParam('login');

			$this->_forward( 'index', 'game', null);die;
		}

    }



}


