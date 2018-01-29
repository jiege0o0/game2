<?php 
$msgtime = max($msg->msgtime,time() - 72*3600);
do{
	$sql = "select * from ".getSQLTable('mail')." where to_gameid='".$userData->gameid."' and time>".$msgtime;
		debug($sql);
	$result = $conne->getRowsArray($sql);
	$returnData->list = $result;
}while(false)
?> 