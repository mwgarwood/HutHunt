<?php
include_once "apiproxy.php";
		
class Detail
{
	private $_display_fields = array(
			array("name"=>"price", "type"=>"dollars", "label"=>"Price:"),
			array("name"=>"type", "type"=>"string", "label"=>"Type:"),
			array("name"=>"bed", "type"=>"string", "label"=>"Bedrooms:"),
			array("name"=>"bath", "type"=>"array", "label"=>"Bathrooms:"),
			array("name"=>"yearBuilt", "type"=>"string", "label"=>"Year Built:"),
			array("name"=>"description", "type"=>"string", "label"=>"Description:"),
			array("name"=>"isNewConstruction", "type"=>"bool", "label"=>"New Construction?"),
			array("name"=>"isForeclosure", "type"=>"bool", "label"=>"In Foreclosure?"),
			array("name"=>"isEcoFriendly", "type"=>"bool", "label"=>"Eco-Friendly?"),
			array("name"=>"isRental", "type"=>"bool", "label"=>"Rental?"),
			array("name"=>"hasOpenHouse", "type"=>"bool", "label"=>"Open House?"),
			array("name"=>"squareFootage", "type"=>"string", "label"=>"Square Footage:"),
			array("name"=>"utilities", "type"=>"array", "label"=>"Utilities:"),
			array("name"=>"interiorDetails", "type"=>"array", "label"=>"Interior Details:"),
			array("name"=>"exteriorDetails", "type"=>"array", "label"=>"Exterior Details:"),
			array("name"=>"schools", "type"=>"array", "label"=>"Schools:"),
			array("name"=>"additionalDetails", "type"=>"array", "label"=>"Additional Details:"),
			array("name"=>"siteVirtualTourUrl", "type"=>"url", "label"=>"View Virtual Tour"),
			array("name"=>"externalUrl", "type"=>"url", "label"=>"")			
	);
	
	public function exec($uri, $args)
	{
		$id = $args["id"];
		$apiproxy = new API_Proxy();
		$api_args["id"] = $id;
		$result = $apiproxy->exec("details", $api_args);
		if ($args["raw"] != null)
		{
			print "<pre>";
			print_r($result);
			return;
		}
		$this->render_page($result["data"]["listing"]);
	}
	
	private function render_page(&$listing)
	{
		$base = HOMEBASE;
		echo <<<EOT
		<!DOCTYPE html>
		<html>
		<head>
		<meta charset="UTF-8">
		<script src=$base/js/jquery-1.7.2.min.js></script>
		<script src=$base/js/lightbox.js></script>
		<link rel="stylesheet" href=$base/css/lightbox.css />
		<link rel="stylesheet" type="text/css" href="$base/css/style.css" />
		<title>Hut Hunter Listing</title>
		</head>
		<body>
		<header>
EOT;
		echo "<h2>";
		$arr = &$listing["address"];
		if ($arr == null)
		{
			echo "An unexpected error has occurred.  No listing found.";
			echo "</h2>";
			echo "</header>";
			return;
		}
		$val = "{$arr["line1"]}, {$arr["city"]}, {$arr["state"]} {$arr["zip"]}";
		if (!empty($arr["county"]))
		{
			$val .= " ({$arr['county']} county)";
		}
		echo "Property Listing for $val";
		echo "</h2>";
		echo "</header>";
		
		echo "<div class=photoarea>";
		$url = $listing['primaryPhoto']['url'];
		if (empty($url))
		{
			$url = "$base/images/imgna.png";
			echo "<img alt='No Photo Available' src=$url>";
		}
		else
		{
			echo "<a href={$listing['primaryPhoto']['urlLarge']} rel='lightbox[photos]'><img alt='Primary Photo' src=$url></a>";
		}
		for ($i = 0; $i < sizeof($listing['photos']); $i++)
		{
			$arr = &$listing['photos'][$i];
			echo "<a href={$arr['urlLarge']} rel='lightbox[photos]'><img alt='Photo $i' src={$arr['url']}></a>";
			
		}
		echo "</div>";
		echo "<table id=listing>";
		for ($i = 0; $i < sizeof($this->_display_fields); $i++)
		{
			$fname = $this->_display_fields[$i]["name"];
			$ftype = $this->_display_fields[$i]["type"];
			$flabel = $this->_display_fields[$i]["label"];
			switch($ftype)
			{
				case "string":
					$val = htmlspecialchars($listing[$fname]);
					break;
				case "bool":
					$val = $listing[$fname] ? "Yes" : "No";
					break;
				case "dollars":
					setlocale(LC_MONETARY, 'en_US');
					$val = money_format("%.0n", $listing[$fname]);
					break;
				case "array":
					if (empty($listing[$fname]))
					{
						$val = null;
					}
					else
					{
						$val = $this->format_array($listing[$fname], "<br>", "");
					}
					break;
				case "url":
					if (empty($listing[$fname]))
					{
						$fname = null;
					}
					elseif (empty($flabel))
					{
						$val = "<a target='_blank' href={$listing[$fname]["url"]}>{$listing[$fname]["label"]}</a>";
					}
					else
					{
						$val = "<a target='_blank' href={$listing[$fname]}>{$flabel}</a>";
						$flabel = "";
					}
					break;
				default:
					$val = null;
					break;
			}
			if (empty($fname))
			{
				continue;
			}
			if (empty($val))
			{
				$val = "Unavailable";
			}
			echo "<tr class=detailitem><td class=listinglabel>$flabel</td> <td class=listingdata>$val</td></tr>";
		}
		echo "</table>";
		echo "</body>";
		echo "</html>";
	}
	
	private function makelabel($str)
	{
		return ucfirst(preg_replace("/([A-Z])/", " $1", $str));
	}
	
	private function format_array(&$arr, $sep, $indent)
	{
		$props = array();
		foreach ($arr as $key=>$value)
		{
			if (gettype($value) == "array")
			{
				$props[] = "<table class=tabdata><tr><td>" . $this->makelabel($key) . ":</td><td>" . $this->format_array($value, $sep, "") . "</td></tr></table>";
			}
			else
			{
				$props[] = $this->makelabel($key) . ": " . htmlspecialchars($value);
			}
		}
		return implode("<br>", $props);
	}
}
?>