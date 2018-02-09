<?php 
	$otherid = $msg->otherid;
	do{
		$sql = "select * from ".getSQLTable('view')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if($result)	
		{
			$obj = json_decode($result['viewlist']);
			unset($obj->{$otherid});
			$sql = "update ".getSQLTable('view')." set viewlist='".json_encode($obj)."' where gameid='".$userData->gameid."'";
			$conne->uidRst($sql);
		}
	}
	while(false);		
?> 