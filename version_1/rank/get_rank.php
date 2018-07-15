<?php 
require_once($filePath."tool/conn.php");
$rankType = $msg->ranktype;
do{
	$sql = "select * from ".getSQLTable('rank_'.$rankType)." where time>0";
	$result = $conne->getRowsArray($sql);
	// foreach($result as $key=>$value)
	// {
		// $result[$key]['score'] = (int)$result[$key]['score'];
	// }
	$returnData->list = $result;
}while(false)
?> 