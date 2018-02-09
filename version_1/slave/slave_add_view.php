<?php 
	$otherid = $msg->otherid;
	do{
		$sql = "select gameid,nick,head,tec_force,hourcoin,type from ".getSQLTable('user_data')." where gameid='".$otherid."')";
		$result2 = $conne->getRowsRst($sql);
	
		$otherInfo= new stdClass();
		$otherInfo->nick = base64_encode($value['nick']);
		$otherInfo->head = $value['head'];
		$otherInfo->tec_force = $value['tec_force'];
		$otherInfo->hourcoin = $value['hourcoin'];
		$otherInfo->type = $value['type'];
		$otherInfo->time = time();
	
		$sql = "select * from ".getSQLTable('view')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if($result)	
		{
			$obj = json_decode($result['viewlist']);
			$obj->{$otherid} = $otherInfo;
			$sql = "update ".getSQLTable('view')." set viewlist='".json_encode($obj)."' where gameid='".$userData->gameid."'";
			$conne->uidRst($sql);
		}
		else
		{
			$obj = new stdClass();
			$obj->{$otherid} = $otherInfo;
			$sql = "insert into ".getSQLTable('view')."(gameid,viewlist) values('".$userData->gameid."','".json_encode($obj)."')";
			$conne->uidRst($sql);
		}
	}
	while(false);		
?> 