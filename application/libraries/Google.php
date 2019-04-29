<?php 

require_once('google-api-php-client-2.2.2/vendor/autoload.php');

class Google {
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
	}

	public function get_login_url()
	{
		return  $this->client->createAuthUrl();
	}

	public function validate() 
	{
		if (isset($_GET['code'])) 
		{
			$token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
			$_SESSION['id_token_token'] = $token;
		}

		if (!empty($_SESSION['id_token_token']) && isset($_SESSION['id_token_token']['id_token'])) 
		{
			$this->client->setAccessToken($_SESSION['id_token_token']);
		}

		if ($this->client->getAccessToken()) 
		{
			$token_data = $this->client->verifyIdToken();
			if ($token_data) 
			{
				$info['id'] = $token_data['sub'];
				$info['email'] = $token_data['email'];
				$info['picture'] = $token_data['picture'] . '?sz=800';
				$info['given_name'] = $token_data['given_name'];
				$info['family_name'] = $token_data['family_name'];
				
				return  $info;
			}
		}
	}
}