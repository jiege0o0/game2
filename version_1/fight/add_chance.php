<?php 
	$need = 30;
	do{
		if($userData->diamond < $need)
		{
			$returnData->fail = 1;
			$returnData->sync_diamond = $userData->diamond;
			break;
		}
		
		$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		$info = json_decode($result['info']);

		require_once($filePath."fight/get_award_card.php");
		$info->award = $getAwardCard;
		
		$returnData->award = $info->award;
		
		$userData->addDiamond(-$need);
		
		$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);

	}while(false);
	
?> 