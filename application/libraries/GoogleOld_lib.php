<?php 

require_once('google_old/Google_Client.php');
require_once('google_old/contrib/Google_Oauth2Service.php');


class GoogleOld_lib
{
    protected $CI;
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('session');
		$this->CI->config->load('google_config');

		$this->client = new Google_Client();
		$this->client->setClientId($this->CI->config->item('google_client_id'));
		$this->client->setClientSecret($this->CI->config->item('google_client_secret'));
		$this->client->setRedirectUri($this->CI->config->item('google_redirect_url'));
		$this->client->setScopes(array(
			"https://www.googleapis.com/auth/plus.login",
			"https://www.googleapis.com/auth/plus.me",
			"https://www.googleapis.com/auth/userinfo.email",
			"https://www.googleapis.com/auth/userinfo.profile"
			)
        );
        $this->google_oauthV2 = new Google_Oauth2Service($this->client);
    }
    
    public function get_login_url()
	{
		return  $this->client->createAuthUrl();
    }
    
    public function validate() 
	{
		if (isset($_GET['code'])) 
		{
			$token = $this->client->authenticate($_GET['code']);
			$_SESSION['id_token_token'] = $token;
		}

		if (!empty($_SESSION['id_token_token']) && isset($_SESSION['id_token_token']['id_token'])) 
		{
			$this->client->setAccessToken($_SESSION['id_token_token']);
		}

		if ($this->client->getAccessToken()) 
		{
			$user_data = $this->google_oauthV2->userinfo->get();
			if ($user_data) 
			{
				return $user_data;
			}
		}
	}
}