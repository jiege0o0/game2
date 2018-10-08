<?php 
	require($filePath."cache/base.php");
	require($filePath."cache/task.php");
	
	$taskID = $msg->taskid;
	$task = $userData->active->task;
	// $line = $task_base[$taskID]['line'];
	$finishTask = $userData->active->task->award;
	
	
	
	
	$arr = array();
	foreach($task_base as $key=>$value)
	{
		// if($value['line'] == $line)
		// {
			// $temp = explode('_',$key,2);
			array_push($arr,$value);
		// }
	}
	
	function sortByIndex($a,$b){
		if($a['index'] < $b['index'])
			return -1;
		return 1;
	}
	
	usort($arr,sortByIndex);
	
	do{
		//是不是正在进行的任务
		if($finishTask)
		{
			$b = false;
			$len = count($arr);
			for($i=0;$i<$len;$i++)
			{
				if($arr[$i]['id'] == $finishTask)
				{
					if($arr[$i+1]['id'] == $taskID)
						$b = true;
					break;
				}
			}
			if(!$b)
			{
				$returnData->fail = 1;
				$returnData->sync_task = $userData->active->task;
				break;
			}	
		}
		else
		{
			if($taskID != $arr[0]['id'])
			{
				$returnData->fail = 1;
				break;
			}
		}
		
		$taskVO = $task_base[$taskID];
		//任务前置条件
		// if($taskVO['level'])
		// {
			// if($line == 2 && $userData->level < $taskVO['level'])
			// {
				// $returnData->fail = 2;
				// $returnData->sync_task = $userData->active->task;
				// break;
			// }
		// }
		
		//任务是否完成
		$isFinsih = false;
		$stat = $userData->active->task->stat;
		$value1 = $taskVO['value1'];
		$value2 = $taskVO['value2'];
		
		if(!$stat)
			$stat = new stdClass();
		switch($taskVO['type'])
		{
		
		 case 'hang':
                $isFinsih = $userData->hang->level >= $value1;
                break;
            case 'force':
                $isFinsih = $userData->tec_force >= $value1;
                break;
            case 'coin':
                $isFinsih = $userData->hourcoin >= $value1;
                break;
            case 'slave':
				$sql = "select awardtime from ".getSQLTable('slave')." where master='".$userData->gameid."' and gameid!='".$userData->gameid."' ORDER BY awardtime ASC";
				$result = $conne->getRowsArray($sql);
				if($result && count($result) >= $value1)
				{
					$isFinsih = true;
				}
                break;
            case 'pvp':
               $sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
				$result = $conne->getRowsRst($sql);
				if($result)
				{
					$offline = json_decode($result['offline']);
					if($offline->score >= $value1)
						$isFinsih = true;
				}
                break;
            case 'resource':
			   $count = 0;
			   foreach($userData->tec as $key=>$value)
				{
					if($tec_base[$key]['type'] == 4)
					{
						$count += $value;
					}
				}
				
				$isFinsih = $count >= $value1;
                break;
            case 'cardnum':
				$count = 0;
				foreach($monster_base as $key=>$value)
				{
					$skillID = (int)$key;
					if($skillID < 200 && ($value['level'] == 0 || in_array($skillID,$userData->card->monster,true)))
					{
						$count ++;
					}
				}
				$isFinsih = $count >= $value1;
                break;
		}
		if(!$isFinsih)
		{
			$returnData->fail = 3;
			$returnData->sync_task = $userData->active->task;
			break;
		}
		//发放奖励
		$award = new stdClass();

		if($taskVO['diamond'])
		{
			$userData->addDiamond($taskVO['diamond']);
			$award->diamond = $taskVO['diamond'];
		}
		if($taskVO['coin'])
		{
			$userData->addCoin($taskVO['coin']);
			$award->coin = $taskVO['coin'];
		}
		if($taskVO['energy'])
		{
			$userData->addEnergy($taskVO['energy']);
			$award->energy = $taskVO['energy'];
		}
		if($taskVO['hero'])
		{
			$awardNum = $taskVO['hero'];
			require_once($filePath."pay/box_hero.php");
		}
		if($taskVO['skill'])
		{
			$awardSkillNumOnce = $taskVO['skill'];
			$awardNum = 1;
			require_once($filePath."pay/box_skill.php");
		}
		
		if($taskVO['prop101'])
		{
			$award->props = array();
			$award->props[101] = $taskVO['prop101'];
			$userData->addProp(101,$taskVO['prop101']);
		}
		
		$returnData->award = $award;
		
		$userData->active->task->award = $taskID;
		$returnData->sync_task = array();
		$returnData->sync_task['award'] = $taskID;
		$userData->setChangeKey('active');
		
	
	}while(false);

?> 