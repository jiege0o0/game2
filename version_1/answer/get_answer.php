<?php 
	require_once($filePath."active/current_active.php");
	do{
		if(!$currentActive || $currentActive['type'] != 2)
		{
			$returnData->fail = 1;
			break;
		}
		require_once($dataFilePath."active/".$currentActive['v1'].".php");
		$returnData->question=$question;
		
		$sql = "select * from ".getSQLTable('answer')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if($result && $result['time'] > $currentActive['start'])
		{
			$returnData->info = json_decode($result['info']);
			break;
		}
		
		$awardNum = 1;
		require($filePath."active/init_award.php");
		
		$info = new stdClass();
		$info->index = 0;//当前步骤
		$info->num = 5;//免费次数
		$info->win_award = $award;//奖励
		
		if($result)
			$sql = "update ".getSQLTable('answer')." set info='".json_encode($info)."',time=".time()." where gameid='".$userData->gameid."'";
		else
			$sql = "insert into ".getSQLTable('answer')."(gameid,info,time) values('".$userData->gameid."','".json_encode($info)."',".time().")";
		$conne->uidRst($sql);

		
	}while(false);
	
?> 