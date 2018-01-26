<?php 
do{
	$sql = "select * from ".getSQLTable('slave')." where gameid='".$msg->gameid."'";
	$result = $conne->getRowsRst($sql);
	$addTime = $result['addtime'];
	if($result['gameid'] == $result['master'])
		$sql = "update ".getSQLTable('user_open')." set masterstep='0|".$addTime."',slavetime=".$addTime." where gameid='".$msg->gameid."'";
	else
		$sql = "update ".getSQLTable('user_open')." set masterstep='1|".$addTime."',slavetime=".$addTime." where gameid='".$msg->gameid."'";
	$conne->uidRst($sql);
}while(false)
?> 