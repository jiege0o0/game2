<?php 
	require_once($filePath."tool/conn.php");
	$id=$msg->id;
	$serverid=$msg->serverid;
	$cdKey=$msg->cdkey;
	$nick=$msg->nick;
	if(testCDKey($msg->id,$msg->cdkey) == false)
	{
		$returnData -> fail = 1;
	}
	else
	{
		$sql = "select * from user where id=".$id;
		$mainUserData = $conne->getRowsRst($sql);
		if($mainUserData)
		{
			if($mainUserData['server'])
				$serverList = split(',',$mainUserData['server']);
			else
				$serverList = array();
			
			$key = $serverid.'|'.$nick;			
			//写用户数据
			if(!in_array($key,$serverList))//已存在
			{
				array_push($serverList,$key);
				$sql = "update user set server='".join(',',$serverList)."' where id='".$id."'";
				$conne->uidRst($sql);
			}
		}
	}
	
	
?> 
