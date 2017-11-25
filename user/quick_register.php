<?php 
require_once($filePath."tool/conn.php");
$quick_password = rand(1,99999999);
$password = rand(100000,999999);
$time = time();
$username = 'g'.($time-1499502006).rand(1,999).'@game.com';

$sql = "insert into user(name,password,last_land,quick_password) values('".$username."','".$password."',".$time.",'".$quick_password."')";

$num = $conne->uidRst($sql);
if($num == 1){
	$sql = "select last_insert_id() as id";
	$result = $conne->getRowsRst($sql,'id');
	$result['password'] = $password;
	$result['name'] = $username;
	$result['last_land'] = $time;
	$result['cdkey'] = getCDKey($result['id'],$time);
	
	$returnData->data = $result;
	$returnData->quick_password = $quick_password;
}
else
{
	$returnData -> fail = 1;
}
?> 