<?php 
	$list = explode(",",$msg->list);
	do{
		$sql = "select * from ".getSQLTable('choose')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		$info = json_decode($result['info']);
		$orgin = explode(",",$info->cardlist);
		
		if(count($list) != count($orgin))
		{
			$returnData->fail = 1;
			break;
		}
		sort($list);
		sort($orgin);
		if(join(",",$list) != join(",",$orgin))
		{
			$returnData->fail = 2;
			break;
		}
		$info->cardlist = $msg->list;
		$returnData->list = $info->cardlist;
		
		$sql = "update ".getSQLTable('choose')." set info='".json_encode($info)."',time=".time()." where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);
		
	}while(false);
	
?> 