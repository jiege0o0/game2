<?php 
	$need = 30;
	do{
		if($userData->diamond < $need)
		{
			$returnData->fail = 1;
			$returnData->sync_diamond = $userData->diamond;
			break;
		}
		
		$sql = "select * from ".getSQLTable('choose')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		$info = json_decode($result['info']);

		require($filePath."choosecard/random_choosecard.php");
		$info->num = 5;//Ãâ·Ñ´ÎÊý
		$info->choose = $skillArr;
		$info->cardlist = '';
		
		$returnData->num = $info->num;
		$returnData->choose = $info->choose;
		
		$userData->addDiamond(-$need);
		
		$sql = "update ".getSQLTable('choose')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);

	}while(false);
	
?> 