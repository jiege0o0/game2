<?php 
$filePath = dirname(__FILE__).'/';
require_once($filePath."_config.php");
	
	
$connect=mysql_connect($sql_url,$sql_user,$sql_password)or die('message=F,Could not connect: ' . mysql_error()); 
mysql_select_db($sql_db,$connect)or die('Could not select database'); 
mysql_query("set names utf8");


mysql_query("
Create TABLE g2_user(
id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
name varchar(64) NOT NULL,
password varchar(32),
quick_password varchar(16),
last_land INT UNSIGNED,
server varchar(500) default '',
UNIQUE (name)
)",$connect)or die("message=F,Invalid query: " . mysql_error()); 

mysql_query("alter table g2_user AUTO_INCREMENT=10000",$connect)or die("message=F,Invalid query: " . mysql_error()); 

echo "成功".time();
?>