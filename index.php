<?php
include "src/route.php";
include "src/home.php";
include "src/search.php";
include "src/detail.php";

$route = new Route(HOMEBASE);
$route->add("", "Home");
$route->add("search", "Search");
$route->add("detail", "Detail");

$route->exec();

?>
