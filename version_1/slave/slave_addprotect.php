<?php 
$otherid=$msg->otherid;
$hour=$msg->hour;
do{
	$sql = "select * from ".getSQLTable('slave')." where gameid='".$otherid."'";
	$result = $conne->getRowsRst($sql);
	if($result['master'] != $userData->gameid)
	{
		$returnData -> fail = 2;
		$returnData -> otherid = $otherid; 
		break;
	}
	
	$begin = max(0,$result['protime']-time());
	$begin = round($begin/3600);
	$count = 3 + $begin;
	for($i=1;$i<$hour;$i++)
	{
		$count += 3+$i + $begin;
	}
	
	if($userData->diamond < $count)//²»¹»×êÊ¯
	{
		$returnData -> fail = 1;
		break;
	}
	$userData->addDiamond(-$count);
	$protime = max($result['protime'],time()) + $hour*3600;
	$returnData->protime = $protime;
	
	$sql = "update ".getSQLTable('slave')." set protime=".$protime." where gameid='".$otherid."'";
	$conne->uidRst($sql);
	
}while(false)
?> 