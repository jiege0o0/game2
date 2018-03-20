<?php 
$msgtime = max($msg->time,time() - 72*3600);
do{
	$sql = "select * from ".getSQLTable('pk_recode')." where gameid='".$userData->gameid."' and time>".$msgtime;
		debug($sql);
	$result = $conne->getRowsArray($sql);
	$returnData->list = $result;
}while(false)
?> 