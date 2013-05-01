<?php
include "config.php";

class SearchForm
{
	private $_args;
	private $_js;
	
	public function __construct($args)
	{
		$this->_args = $args;
		//$this->_js[] = "http://code.jquery.com/jquery-1.9.1.min.js";
		$this->_js[] = HOMEBASE . "/js/jquery-1.7.2.min.js";
		$this->_js[] = HOMEBASE . "/js/searchform.js";
		$this->_js[] = HOMEBASE . "/js/placeholder.js";
		
	}
	
	public function jscript()
	{
		return $this->_js;
	}
	
	public function render()
	{
		$base = HOMEBASE;
		$area = str_replace("\"", "&quot;", $this->_args['Area']);
		$pricemin = preg_replace("/\D+/", "", $this->_args['Minimum_Price']);
		$pricemax = preg_replace("/\D+/", "", $this->_args['Maximum_Price']);
		$sqrt = preg_replace("/\D+/", "", $this->_args['Square_Footage']);
		echo <<<EOT
		<h2>Where would you like to focus your hunt?</h2>
		<form action="$base/search" method=post onsubmit="return validateForm()">
		<input type=text id=area name=Area placeholder="City, State or Zip" value="{$area}" required>
		<input type=text id=pricemin name="Minimum Price" placeholder="Minimum Price" value="{$pricemin}" min=0 max=10000000>
		<input type=text id=pricemax name="Maximum Price" placeholder="Maximum Price" value="{$pricemax}" min=0 max=10000000>
		<input type=text id=squareFootage name="Square Footage" placeholder="Square Footage" value="{$sqrt}" min=0 max=10000>
		<input type=submit value=Hunt>
		<div id=errmsg>{$this->_args['errmsg']}</div>
EOT;
	}
	
	public function include_jscript()
	{
		for ($i = 0; $i < sizeof($this->_js); $i++)
		{
			echo "<script type=\"text/javascript\" src=\"{$this->_js[$i]}\"></script>";
		}
EOT;
	}
}