<?php
# include the function here
require_once 'resize.php';

echo dynamic_image(
	$_GET['url'], ( is_numeric($_GET['w'] ?? '') ? $_GET['w'] : false )
);