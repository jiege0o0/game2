<?php 
	$need = 10;
	do{
		if($userData->diamond < $need)
		{
			$returnData->fail = 1;
			$returnData->sync_diamond = $userData->diamond;
			break;
		}
		
		$sql = "select * from ".getSQLTable('answer')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		$info = json_decode($result['info']);

		$info->num += 5;//Ãâ·Ñ´ÎÊý
		
		$returnData->num = $info->num;
		
		$userData->addDiamond(-$need);
		
		$sql = "update ".getSQLTable('answer')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);

	}while(false);
	
?> 