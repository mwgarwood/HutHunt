<?php

class Route
{
	private $_command = array();
	private $_method = array();
	private $_default_route;
	
	public function __construct($default_route)
	{
		$this->_default_route = $default_route;
	}
	
	public function add($command, $method)
	{
		$this->_command[] = $command;
		$this->_method[] = $method;
	}
	
	public function exec()
	{
		$requestURI = explode("/", $_SERVER["REQUEST_URI"]);
		$scriptNAME = explode("/", $_SERVER["SCRIPT_NAME"]);
		
		for ($i = 0; $i < sizeof($scriptNAME); $i++)
		{
			if ($requestURI[$i] == $scriptNAME[$i])
			{
				unset($requestURI[$i]);
			}
			else
			{
				break;
			}
		}
		$command = array_values($requestURI);
		$routeidx = array_search($command[0], $this->_command);
		if (is_bool($routeidx))
		{
			header("Location: " . $this->_default_route);
		}
		else
		{
			$route = new $this->_method[$routeidx];
			$args = array();
			for ($i = 1; $i < sizeof($command); $i++)
			{
				list($key, $val) = explode("=", $command[$i], 2);
				$args[$key] = urldecode($val);
			}
			$route->exec(HOMEBASE . "/" . $command[0], $args);
		}
	}
}
?>