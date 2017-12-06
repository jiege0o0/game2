<?php
	header('Access-Control-Allow-Origin:*');

	$filePath = dirname(__FILE__).'/user/';

	require_once($filePath."index.php");
?>