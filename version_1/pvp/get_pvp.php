<?php 
	require_once($filePath."cache/base.php");
	do{
		$sql = "select * from ".getSQLTable('pvp_offline')." where gameid='".$userData->gameid."'";
		$returnData->offline = $conne->getRowsRst($sql);
		$conne->close_rst();
		
		
		$sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if($result)
		{
			$task = json_decode($result['task']);
			if(isSameDate($result['time']))
			{
				$returnData->task = $task;
				break;
			}
			
		}
		else
		{
			$task = new stdClass();
			$task->total = 0;
		}

		$task->list = array();
		$list = [1,2,3,4,5,3];
		shuffle($list);
		for($i=0;$i<3;$i++)
		{
			$type = $list[$i];
			$oo = new stdClass();
			$oo->type = $type;
			$oo->exp = rand(5,10);
			$oo->current = 0;
			switch($type)
			{
				case 1://进行5场比赛
					$oo->num = rand(5,10);
					$oo->box = ceil(($oo->num-4)/2);
					break;
				case 2://获得3胜
					$oo->num = rand(3,5);
					$oo->box = ceil(($oo->num-2)/2);
					break;
				case 3://使用XX N次
					$temp = array();
					foreach($monster_base as $key=>$value)
					{
						$skillID = (int)$key;
						if($skillID < 200 && $value['level'] == 0 || in_array($skillID,$userData->card->monster,true))//@skill
							array_push($temp,$skillID);
					}
					$oo->mid = array_rand($temp,1);
					$oo->num = rand(9,15);
					$oo->box = ceil($oo->num/3) - 2;
					break;
				case 4://10费及以上
					$oo->num = rand(15,25);
					$oo->box = floor(($oo->num-10)/5);
					break;
				case 5://5费及以下
					$oo->num = rand(30,50);
					$oo->box = floor(($oo->num-20)/10);
					break;
			}
			array_push($task->list,$oo);
		}

		$returnData->task = $task;
		if($result)
		{
			$sql = "update ".getSQLTable('pvp')." set task='".json_encode($task)."' where gameid='".$userData->gameid."'";
		}
		else
		{	
			$sql = "insert into ".getSQLTable('pvp')."(gameid,task,online,offline,time) values('".$userData->gameid."','".json_encode($task)."','{}','{}',".time().")";
		}
		
		$conne->uidRst($sql);
		
	}while(false);
	
?> 