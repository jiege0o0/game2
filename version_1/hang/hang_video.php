<?php 
require_once($filePath."tool/conn.php");
$sql = "select data from ".getSQLTable('video')." where id=".$msg->id." and time=".$msg->time."";
$result = $conne->getRowsRst($sql);
$returnData->data = $result;
?> 