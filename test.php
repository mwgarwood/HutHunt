<?php
echo "<pre>";
print_r($_SERVER);
print(ini_get("variables_order")) . PHP_EOL;
print(ini_get("request_order")) . PHP_EOL;
print_r($_REQUEST);
print_r($_GET);
