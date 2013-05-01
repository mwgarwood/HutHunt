<?php
include "config.php";
include_once "arginfo.php";

class API_Proxy
{
	private $_baseurl = "http://services.homefinder.com/listingServices";
	
	public function __construct()
	{
		
	}
	
	public function exec($command, $args)
	{
		$arginfo = new ArgInfo($args);
		$arginfo->add(array("apikey"=>APIKEY));
		$url = $this->_baseurl . "/" . $command . "?" . $arginfo->serialize();
		return $this->call($url);
	}
	
	public function call($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$contents = curl_exec($ch);
		$result = json_decode($contents, TRUE);
		curl_close($ch);
		return $result;
	}
}
?>