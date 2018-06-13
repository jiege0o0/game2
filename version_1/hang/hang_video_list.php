<?php 
require_once($filePath."tool/conn.php");
$sql = "select id,info,time from ".getSQLTable('video')." where level=".$msg->level." and time>0";
$result = $conne->getRowsArray($sql);
$returnData->list = $result;
?> 