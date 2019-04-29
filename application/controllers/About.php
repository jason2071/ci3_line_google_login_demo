<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

    function __construct()
	{
		parent::__construct();
		$this->_init();
	}

    private function _init()
	{
		$this->output->set_template('main');
	}

	public function index()
	{
		$this->load->section('aside', 'layout/aside', array("active" => 'about'));
		$this->load->section('footer', 'layout/footer');
		$this->load->view('layout/about');
    }
}