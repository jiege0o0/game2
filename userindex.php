<?php
	header('Access-Control-Allow-Origin:*');
	header('Access-Control-Allow-Headers X-Requested-With');
	header('Access-Control-Allow-Methods GET,POST,OPTIONS');
	require_once(dirname(__FILE__)."/game_version_path.php");
	$filePath = dirname(__FILE__).'/user/';
	require_once($filePath."index.php");
?>