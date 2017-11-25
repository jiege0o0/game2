<?php 
require_once($filePath."tool/conn.php");
$username=$msg->name;
$password=$msg->password;
$sql = "select * from ".getSQLTable('user')." where name='".$username."'";
$num = $conne->getRowsNum($sql);
if($num == 0)//可以注册
{
	$quick_password = rand(1,99999999);
	$time = time();
	$sql = "insert into ".getSQLTable('user')."(name,password,last_land,quick_password) values('".$username."','".$password."',".$time.",'".$quick_password."')";
	$num = $conne->uidRst($sql);
	debug($sql);
	if($num == 1){
		$sql = "select last_insert_id() as id";
		$result = $conne->getRowsRst($sql);
		$result['cdkey'] = getCDKey($result['id'],$time);
		$result['last_land'] = $time;
		$returnData->data = $result;
		$returnData->quick_password = $quick_password;
	}
	else
	{
		$returnData -> fail = 1;
		// errorLog('register:'.json_encode($msg));
	}
}
else //用户名已被使用
{
	$returnData -> fail = 2;
}
?> 