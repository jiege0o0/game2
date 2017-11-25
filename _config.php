<?php
	$sql_url = "127.0.0.1";
	$sql_user = "root";	
	$sql_password = "111111";	
	$sql_db = 'game2';
	
	
	// $sql_url = "qdm218719323.my3w.com";
	// $sql_user = "qdm218719323";	
	// $sql_password = "c3312819";	
	// $sql_db = 'qdm218719323_db';
	
	
	$sql_table = 'no'.$serverID.'_';
	$sql_pre = 'g2_';
	
	function getSQLTable($name){
		global $sql_pre,$sql_table;
		return $sql_pre.$sql_table.$name;
	}
?>