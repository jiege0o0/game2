<?php 
require_once($filePath."cache/base.php"); 
do{
	$sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);
	if($result)
	{
		$offline = json_decode($result['offline']);
		$returnData->pvp = $offline->score;
	}
	$returnData->active = $active_base;
}while(false)

?> 