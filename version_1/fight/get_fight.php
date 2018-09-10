<?php 
	require_once($filePath."active/current_active.php");
	do{
		if(!$currentActive || $currentActive['type'] != 1)
		{
			$returnData->fail = 1;
			break;
		}

		$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if($result && $result['time'] > $currentActive['start'])
		{
			$returnData->info = json_decode($result['info']);
			break;
		}
		$awardNum = 1;
		require($filePath."active/init_award.php");
		
		$info = new stdClass();
		$info->index = 0;//µ±Ç°²½Öè
		$info->win_award = $award;//½±Àø
		$info->final_award = $currentActive['faward'] ;//½±Àø
		$returnData->info = $info;
		
		if($result)
			$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."',time=".time()." where gameid='".$userData->gameid."'";
		else
			$sql = "insert into ".getSQLTable('fight')."(gameid,info,time) values('".$userData->gameid."','".json_encode($info)."',".time().")";
		$conne->uidRst($sql);

		
		
	}while(false);
	
?> 