<?php
class ArgInfo
{
	private $_argv = array();
	
	public function __construct($args)
	{
		$this->add($args);
	}
	
	public function add($args)
	{
		foreach ($args as $key => $val)
		{
			if ($val != null && trim($val) != "")
			{
				$this->_argv[] = $key . "=" . urlencode($val);
			}
		}
	}
	
	public function serialize($delim = "&")
	{
		return join($delim, $this->_argv);
	}
}
?>