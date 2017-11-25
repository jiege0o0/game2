<?php
	header('Access-Control-Allow-Origin:*');

	$serverID = 1;
	date_default_timezone_set("PRC");
	$serverOpenTime = mktime(12,0,0,9,13,2017); //2017-9-8 12:0:0
	require_once(dirname(__FILE__)."/game_version_path.php");
	require_once($filePath."index.php");
?>