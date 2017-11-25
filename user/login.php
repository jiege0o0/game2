<?php 
	require_once($filePath."tool/conn.php");
	$username=$msg->name;
	$password=$msg->password;
	$quick_password=$msg->quick_password;
	if($quick_password)
		$sql = "select * from ".getSQLTable('user')."  where name='".$username."' and quick_password='".$quick_password."'";
	else
		$sql = "select * from ".getSQLTable('user')."  where name='".$username."' and password='".$password."'";
	$mainUserData = $conne->getRowsRst($sql);
	if($mainUserData)//可以登录
	{
		$quick_password = rand(1,99999999);
		$time = time();
		$mainUserData['last_land'] = $time;
		$mainUserData['cdkey'] = getCDKey($mainUserData['id'],$time);
		unset($mainUserData['password']);
		// unset($mainUserData['email']);
		$sql = "update ".getSQLTable('user')." set last_land=".$time.",quick_password='".$quick_password."' where id='".$mainUserData['id']."'";
		if(!$conne->uidRst($sql))
		{
			$returnData->fail = 2;
		}
		else
		{	
			$returnData->quick_password = $quick_password;
			$returnData->userdata = $mainUserData;
		}
	}
	else
	{
		$returnData->fail = 1;
	}
?> 