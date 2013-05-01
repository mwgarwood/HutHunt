<?php

include "apiproxy.php";
include "pager.php";
include_once "searchform.php";

class Search
{
	public function exec($uri, $args)
	{
		if (sizeof($_POST) > 0)
		{
			$args = $_POST + $args;
		}
		$area = trim($args["Area"]);
		$pricemin = trim($args["Minimum_Price"], "$ \t\n\r\0\x0B");
		$pricemax = trim($args["Maximum_Price"], "$ \t\n\r\0\x0B");
		$sqft = trim($args["Square_Footage"]);
		$api_args["area"] = $area;
		if (!empty($pricemin))
		{
			if (empty($pricemax))
			{
				$pricemax = "*";
			}
		}
		elseif (!empty($pricemax))
		{
			$pricemin = "*";
		}
		if ($pricemin)
		{
			$api_args["price"] = $pricemin . " TO " . $pricemax;
		}
		if (!empty($sqft))
		{
			$api_args["squareFootage"] = "$sqft" . " TO " . "*";
		}
		if ($args["page"] > 1)
		{
			$api_args["page"] = $args["page"];
		}
		$apiproxy = new API_Proxy();
		$result = $apiproxy->exec("search", $api_args);
		if ($args["raw"] != null)
		{
			print ("<pre>");
			print_r($result);
			return;
		}
		$rc = $result["status"]["code"];
		if ($rc != 200 || empty($result["data"]["listings"]))
		{
			$this->render_error("Your search did not return any results. Please try again.", $args);
			return;
		}
		else
		{
			$page = $result["data"]["meta"]["currentPage"];
			$num_pages = $result["data"]["meta"]["totalPages"];
			$num_found = $result["data"]["meta"]["totalMatched"];
			$type = $result["data"]["meta"]["area"]["type"];
			$name = $result["data"]["meta"]["area"]["name"];
			$state = $result["data"]["meta"]["area"]["state"];
			$area = ($type == "city" ? $name . ", " . $state : $name);
		}
		$pager = new Pager($page, $num_pages, $uri, $args);
		$this->render_head($pager, $num_found, $area);
		$this->render_body($result["data"]["listings"]);
		$this->render_footer($pager);
	}
	
	private function render_head(&$pager, $numfound, $area)
	{
		$base = HOMEBASE;
		echo <<<EOT
		<!DOCTYPE html>
		<html>
		<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="$base/css/style.css">
		<title>Hut Hunter Search Results</title>
		</head>
		<body>
		<header>
EOT;
		echo "<h2>";
		
		if ($pager->_curpage == 1)
		{
			echo "Your search based on the area: $area has yielded $numfound results. ";
		}
		else
		{
			echo "Area: $area ";
		}
		if ($pager->_numpages > 1)
		{
			echo "(Page {$pager->_curpage} of {$pager->_numpages})";
		}
		echo "</h2>";
		$pager->render();
		echo "</header>";
	}
	
	private function render_body(&$listings)
	{
		$base = HOMEBASE;
		$disp_fields = array("address", "type", "description", "price");
		echo "<table class=listings>";
		for ($i = 0; $i < sizeof($listings); $i++)
		{
			$url = $listings[$i]["primaryPhoto"]["url"];
			if (!$url)
			{
				$url = "$base/images/imgna.png";
			}
			echo "<tr><td><a class=photolink href=$base/detail/id={$listings[$i]['id']}><img Alt='Primary Photo' src=$url></a></td>";
			echo "<td>";
			for ($f = 0; $f < sizeof($disp_fields); $f++)
			{
				$field = $disp_fields[$f];
				switch ($field)
				{
					case "address":
						$arr = &$listings[$i][$field];
						$val = "{$arr["line1"]}, {$arr["city"]}, {$arr["state"]} {$arr["zip"]}";
						break;
					case "price":
						setlocale(LC_MONETARY, 'en_US');
						$val = money_format("%.0n", $listings[$i][$field]);
						break;
					default:
						$val = $listings[$i][$field];
						break;
				}
				$fname = ucfirst($field);
				$val = htmlspecialchars($val);
				echo "<div class=$field><span>$fname: </span><span>$val</span></div>";
			}
			echo "<div class=dlink><a href=$base/detail/id={$listings[$i]['id']}>Get more information!</a></div>";
			echo "</td></tr>";
		}
		echo "</table>";
	}
	
	private function render_footer(&$pager)
	{
		echo "<footer>";
		$pager->render();
		echo "</footer>";
		echo "</body></html>";
	}
	
	private function render_error($errmsg, $args)
	{
		$args["errmsg"] = $errmsg;
		$form = new SearchForm($args);
		$base = HOMEBASE;
		echo <<<EOT
		<!DOCTYPE html>
		<html>
		<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="$base/css/style.css">
		<title>Hut Hunter Search Results - Empty</title>
EOT;
		$form->include_jscript();
		echo "</head><body>";
		$form->render();
		echo "</body></html>";
	}
}
?>