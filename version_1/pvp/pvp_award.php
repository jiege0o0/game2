<?php 
	$index = $msg->index;
	$sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);
	$task = json_decode($result['task']);
	
	do{	
		if($index == 0)//´ó½±
		{
			if(!$task->total || $task->total < 100)
			{
				$returnData -> fail = 1;
				break;
			}
			
			$task->total -= 100;
			break;
		}
		
		$index--;
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
		$task->total += $task->list[$index]->exp;
		
		$awardNum = $task->list[$index]->box;
		require_once($filePath."pay/box_resource.php");
		$award->pvp_exp = $task->list[$index]->exp;
		
		$sql = "update ".getSQLTable('pvp')." set task='".json_encode($task)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);

	}while(false);
	
?> 