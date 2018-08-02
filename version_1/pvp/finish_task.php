<?php 
	$useObj = new stdClass();
	$num5 = 0;
	$num10 = 0;
	foreach($playerData->list as $key=>$value)
	{
		$id = $value["mid"];
		if($useObj->{$id})
			$useObj->{$id} +=1;
		else
			$useObj->{$id} = 1;
		
		if($monster_base[$id]['cost'] >= 10)
			$num10 ++;
		else if($monster_base[$id]['cost'] <= 5)
			$num5 ++;
	}

	for($i=0;$i<4;$i++)
	{
		$type = $task->list[$i]->type;
		switch($type)
		{
			case 1://进行5场比赛
				$task->list[$i]->current ++;
				break;
			case 2://获得3胜
				if($isPKWin)
					$task->list[$i]->current ++;
				break;
			case 3://使用XX N次
				$num = $useObj->{$task->list[$i]->mid};
				if($num)
					$task->list[$i]->current += $num;
				break;
			case 4://10费及以上
				$task->list[$i]->current += $num10;
				debug($num10);
				break;
			case 5://5费及以下
				$task->list[$i]->current += $num5;
				break;
		}
	}			
?> 