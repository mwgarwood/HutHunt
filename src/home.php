<?php 
include "searchform.php";

class Home
{
	public function exec($uri, $args)
	{
		$form = new SearchForm($args);
		$js = $form->jscript();
		echo <<<EOT
		<!DOCTYPE html>
		<html>
		<head>
		<title>Hut Hunter Search</title>
		<meta charset="UTF-8">
EOT;
		$form->include_jscript();
		echo "</head><body>";
		$form->render();
		echo "</body></html>";
	}			
}
?>
