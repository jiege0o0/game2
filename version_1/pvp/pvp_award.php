<?php 
	$index = $msg->index;
	$sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);
	$task = json_decode($result['task']);
	
	do{	
		
		if(!$task->list[$index])
		{
			$returnData -> fail = 2;
			break;
		}
		
		if($task->list[$index]->award)
		{
			$returnData -> fail = 3;
			break;
		}
		
		if($task->list[$index]->current < $task->list[$index]->num)
		{
			$returnData -> fail = 4;
			break;
		}
		
		$task->list[$index]->award = 1;
		if($task->list[$index]->box)
		{
			$awardNum = $task->list[$index]->box;
			require_once($filePath."pay/box_resource.php");
		}
		else if($task->list[$index]->skill)
		{
			$awardNum = $task->list[$index]->skill;
			require_once($filePath."pay/box_skill.php");
		}
		else if($task->list[$index]->hero)
		{
			$awardNum = $task->list[$index]->hero;
			require_once($filePath."pay/box_hero.php");
		}
		
		if($index != 0)
			$task->list[0]->current ++;
		
		$returnData->task = $task;
		$sql = "update ".getSQLTable('pvp')." set task='".json_encode($task)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);

	}while(false);
	
?> 