<?php //请求以同步数据
require_once($filePath."tool/conn.php");
do{
$time = time();

//7天前的注册
	$sql = "select count(*) from ".getSQLTable('user_data')." where regtime>".($time-7*24*3600)."";
	$returnData->total = $conne->getRowsRst($sql);
	
	$sql = "select count(*) from ".getSQLTable('user_data')." where regtime-last_land >".(24*3600)." and regtime<";
	$returnData->total = $conne->getRowsRst($sql);
	
}while(false)
?> 