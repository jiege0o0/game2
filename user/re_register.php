<?php 
require_once($filePath."tool/conn.php");
$username=$msg->name;
$password=$msg->password;
$lastPassword=$msg->last_password;
$lastName=$msg->last_name;
$sql = "select * from user where name='".$username."'";
$num = $conne->getRowsNum($sql);
if($num == 0)//可以注册
{
	$time = time();
	$sql = "update user set name='".$username."',password='".$password."',last_land=".$time." where name='".$lastName."' and password='".$lastPassword."'";
	$num = $conne->uidRst($sql);	
	if($num == 1){
		$sql = "select * from user where name='".$username."' and password='".$password."'";
		$mainUserData = $conne->getRowsRst($sql);
		$mainUserData['last_land'] = $time;
		$mainUserData['cdkey'] = getCDKey($mainUserData['id'],$time);
		unset($mainUserData['password']);
		$returnData->userdata = $mainUserData;
	}
	else
	{
		$returnData -> fail = 2;
		errorLog('re_register:'.json_encode($msg));
	}
}
else
	$returnData -> fail = 1;
?> 