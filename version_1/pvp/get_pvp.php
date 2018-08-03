<?php 
	require_once($filePath."cache/base.php");
	do{
		// $sql = "select * from ".getSQLTable('pvp_offline')." where gameid='".$userData->gameid."'";
		// $returnData->offline = $conne->getRowsRst($sql);
		// $conne->close_rst();
		
		
		$sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if($result)
		{
			$task = json_decode($result['task']);
			$online = json_decode($result['online']);
			$offline = json_decode($result['offline']);
			debug($result['time']);
			if(isSameDate($result['time']))
			{
				$returnData->task = $task;
				$returnData->online = $online;
				$returnData->offline = $offline;
				break;
			}
			
		}
		else
		{
			$task = new stdClass();
			$online = new stdClass();
			$offline = new stdClass();
			$task->total = 0;
		}

		$task->list = array();
		$list = array(1,2,3,4,5,3);
		shuffle($list);
		array_unshift($list,0);
		for($i=0;$i<4;$i++)
		{
			$type = $list[$i];
			$oo = new stdClass();
			$oo->type = $type;
			$oo->current = 0;
			switch($type)
			{
				case 0://完成所有任务
					$oo->num = 3;
					$oo->box = 3;
					break;
				case 1://进行5场比赛
					$oo->num = rand(5,10);
					$oo->box = $oo->num > 7.5?2:1;
					break;
				case 2://获得3胜
					$oo->num = rand(3,5);
					$oo->box = $oo->num > 4?2:1;
					break;
				case 3://使用XX N次
					$temp = array();
					foreach($monster_base as $key=>$value)
					{
						$skillID = (int)$key;
						if($skillID < 200 && $value['level'] == 0 || in_array($skillID,$userData->card->monster,true))//@skill
							array_push($temp,$skillID);
					}
					debug($temp);
					$oo->mid = $temp[rand(0,count($temp)-1)];
					$oo->num = rand(9,15);
					$oo->box = $oo->num > 12?2:1;
					break;
				case 4://10费及以上
					$oo->num = rand(15,25);
					$oo->box = $oo->num > 20?2:1;
					break;
				case 5://5费及以下
					$oo->num = rand(30,50);
					$oo->box = $oo->num > 40?2:1;
					break;
			}
			array_push($task->list,$oo);
		}

		$returnData->task = $task;
		$returnData->online = $online;
		$returnData->offline = $offline;
		if($result)
		{
			$sql = "update ".getSQLTable('pvp')." set task='".json_encode($task)."',time=".time()." where gameid='".$userData->gameid."'";
		}
		else
		{	
			$sql = "insert into ".getSQLTable('pvp')."(gameid,task,online,offline,time) values('".$userData->gameid."','".json_encode($task)."','{}','{}',".time().")";
		}
		
		$conne->uidRst($sql);
		
	}while(false);
	
?> 