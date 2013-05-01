<?php
include_once "arginfo.php";

class Pager
{
	public $_curpage;
	public $_numpages;
	public $_numhandles = 10;
	private $_firsthandle;
	private $_lasthandle;
	private $_baseurl;
	
	public function __construct($curpage, $numpages, $uri, $args)
	{
		$this->_curpage = $curpage;
		$this->_numpages = $numpages;
		unset($args["page"]);
		$arginfo = new ArgInfo($args); 
		$this->_baseurl = $uri . "/" . $arginfo->serialize("/");
		if ($numpages < $this->_numhandles)
		{
			$this->_numhandles = $numpages;
			$this->_firsthandle = 1;
			$this->_lasthandle = $numpages;
		}
		else
		{
			$this->_firsthandle = $curpage - $this->_numhandles/2;
			if ($this->_firsthandle < 1)
			{
				$this->_firsthandle = 1;
				$this->_lasthandle = $this->_numhandles;
			}
			else
			{
				$this->_lasthandle = $this->_firsthandle + $this->_numhandles - 1;
				if ($this->_lasthandle > $numpages)
				{
					$this->_lasthandle = $numpages;
					$this->_firsthandle = $numpages - $this->_numhandles + 1;
				}
			}
		}
	}
	
	public function render()
	{
		if ($this->_numpages <= 1)
			return;
		echo "<div class=pager>";
		if ($this->_curpage > 1)
		{
			echo "<button class=pelem onclick=\"window.location='{$this->pagelink($this->_curpage-1)}'\">&lt;&lt;Prev</button>";
		}
		for ($i = $this->_firsthandle; $i <= $this->_lasthandle; $i++)
		{
			if ($i == $this->_curpage)
			{
				echo "<span class=pelem>" . $i . "</span>";
			}
			else
			{
				echo "<a class=pelem href={$this->pagelink($i)}>$i</a>";
			}
		}
		if ($this->_curpage < $this->_numpages)
		{
			echo "<button class=pelem onclick=\"window.location='{$this->pagelink($this->_curpage+1)}'\">Next&gt;&gt;</button>";
		}
		echo "</div>";
	}
	
	private function pagelink($pagenum)
	{
		return $this->_baseurl . "/page=$pagenum";
	}
}
?>