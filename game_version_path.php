<?php
	//��ͬ�汾����ͬ�ķ�����
	if($_POST['game_version'] == 2)
		$filePath = dirname(__FILE__).'/version_2/';
	else
		$filePath = dirname(__FILE__).'/version_1/';
		
	$dataFilePath = dirname(__FILE__).'/';	
	
	require_once(dirname(__FILE__).'/_config.php');
?>