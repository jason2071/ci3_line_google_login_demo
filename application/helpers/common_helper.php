<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('callapipost') ) 
{
	function callapipost($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$result=curl_exec ($ch);

		curl_close ($ch);
		return $result;

	}
}

if ( !function_exists('callapi') ) 
{
	function callapi($url)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$curl_response = curl_exec($curl);
		if ($curl_response === false) {
		    $info = curl_getinfo($curl);
		    curl_close($curl);
		    die('error occured during curl exec. Additioanl info: ' . var_export($info));
		}
		curl_close($curl);
		$result = json_decode($curl_response,true);
		return $result;
	}
}