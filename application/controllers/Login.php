<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct()
	{
        parent::__construct();
        // $this->load->library('google');
        // $this->load->library('linelogin_lib', array(
        //     'client_id' => '1647379540', 
        //     'client_secret' => '0352f1c1651471857f9447b17591cac6',
        //     'callback_url' => base_url() . 'login/line_callback'
        // ));

        $this->load->library('GoogleOld_lib', null, 'google');
    	$this->_init();
	}

    private function _init()
	{
		$this->output->set_template('login');
	}

	public function index()
	{
        $data['get_login_url'] = $this->google->get_login_url();
        $this->load->view('layout/login', $data);
    }

    public function line()
    {
        $this->output->unset_template();
        if(!isset($_SESSION['ses_login_accToken_val'])){    
            $this->linelogin_lib->authorize(); 
            exit;
        }
    }

    public function line_callback() {
        $this->output->unset_template();

        $dataToken = $this->linelogin_lib->requestAccessToken($_GET, true);
        
        if(!is_null($dataToken) && is_array($dataToken))
        {
            if(array_key_exists('access_token',$dataToken))
            {
                $_SESSION['ses_login_accToken_val'] = $dataToken['access_token'];
            }
            
            if(array_key_exists('refresh_token',$dataToken))
            {
                $_SESSION['ses_login_refreshToken_val'] = $dataToken['refresh_token'];
            }  

            if(array_key_exists('id_token',$dataToken))
            {
                $_SESSION['ses_login_userData_val'] = $dataToken['user'];
            }       
        }

        $accToken = $_SESSION['ses_login_accToken_val'];
        $userInfo = $this->linelogin_lib->userProfile($accToken, true);

        if(!is_null($userInfo) && is_array($userInfo) && array_key_exists('userId',$userInfo))
        {
            echo '<pre>';
            print_r($userInfo);
        }

        die();
    }

    public function oauth2callback() {
        $this->output->unset_template();

        $google_data = $this->google->validate();
        
        if ($avatar = $this->get_google_profile_picture('avt/'.date('Y/m/d'), $google_data['picture']."?sz=500")) 
        {
            echo $avatar."<br>";
        } else {
            echo "avatar : false";
        }
        
        echo '<pre>';
        print_r($google_data);
        die();
    }

    private function get_google_profile_picture($path, $picture)
	{
		$avatar = $path.'/'.uniqid().'.jpg';

		if (get_picture($picture, '123465')) 
		{
			return $avatar;
		} else {
			return false;
		}
	}
}