<?php 
require_once($filePath."tool/conn.php");
$rankType = $msg->ranktype;
do{
	$sql = "select * from ".getSQLTable('rank_'.$rankType)." where time>0";
	$result = $conne->getRowsArray($sql);
	$returnData->list = $result;
}while(false)
?> 