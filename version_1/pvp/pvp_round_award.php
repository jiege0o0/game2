<?php 
	require_once($filePath."pvp/pvp_tool.php");
	$type = $msg->type;
	$sql = "select ".$type." from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);
	$data = json_decode($result[$type]);
	
	do{	
		
		if(!$data->award)
		{
			$returnData -> fail = 1;
			break;
		}
		
		$awardNum = getPVPLevel($data->award)*5;
		require_once($filePath."pay/box_resource.php");
	
		$data->award = 0;
		$returnData->award = $award;
		$sql = "update ".getSQLTable('pvp')." set ".$type."='".json_encode($data)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);

	}while(false);
	
?> 