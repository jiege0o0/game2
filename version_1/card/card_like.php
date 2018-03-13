<?php 
require_once($filePath."tool/conn.php");
$id = $msg->id;
do{
	$sql = "select * from ".getSQLTable('card_like')." where id=".$id;
	$result = $conne->getRowsRst($sql);
	$returnData -> data = $result;
}while(false)

?> 