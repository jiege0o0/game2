<?php 
	do{
		$sql = "select * from ".getSQLTable('random')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		$info = json_decode($result['info']);
		
		if($info->get_final_award)
		{
			$returnData->fail = 1;
			break;
		}
		if($info->index != 12)
		{
			$returnData->fail = 2;
			break;
		}

		if($info->final_award == 'box')
		{
			$awardNum = 20;
			require_once($filePath."pay/box_resource.php");
		}
		else if($info->final_award == 'skill')
		{
			$awardNum = 2;
			require_once($filePath."pay/box_skill.php");
		}
		else if($info->final_award == 'hero')
		{
			$awardNum = 1;
			require_once($filePath."pay/box_hero.php");
		}
		
		$info->get_final_award = true;
		$info->win_award = $award;
		
		$sql = "update ".getSQLTable('random')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);

	}while(false);
	
?> 