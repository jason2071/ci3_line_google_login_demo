<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    function __construct()
	{
		parent::__construct();
		//$this->load->model('Booxroox', 'mBooxroox');
		//$this->_init();
	}

    private function _init()
	{
		//$this->output->set_template('main');
	}

	public function index()
	{
		//$this->load->section('aside', 'layout/aside', array("active" => 'home'));
		//$this->load->section('footer', 'layout/footer');
		//$this->load->view('layout/home');

		//$data = $this->mBooxroox->getLastData();

		//echo '<pre>';
		//print_r($data);
		$picture = "https://lh4.googleusercontent.com/-zeYlGvu9qpk/AAAAAAAAAAI/AAAAAAAAAAA/ACevoQMX4ey4cVOcdYxHnuG0OEGMDSMAuA/mo/photo.jpg?sz=500";
		$tmpname = mediatmpname('avt');

		$dst_img = @imagecreatefromjpeg($picture);

		echo "dst_img -> ".$dst_img.'<br>';

		if ($dst_img) {
			echo "true";
		} else {
			echo "false";
		}
	}
}