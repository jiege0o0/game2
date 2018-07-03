<?php 
	$id = $msg->id;
	do{
		$sql = "select * from ".getSQLTable('shop')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if(!$result || !isSameDate($result['time']))
		{
			$returnData->fail = 1;
			break;
		}
		$arr = json_decode($result['shop']);
		// debug($result['shop']);
		// debug($arr);
		// break;
		
		foreach($arr as $key=>$value)
		{
			if($value->id == $id)
			{
				$shopValue = $value;
				$shopKey = $key;
				break;
			}
		}
		
		if(!$shopValue)
		{
			$returnData->fail = 2;
			break;
		}
		if(!$shopValue->times)
			$shopValue->times = 0;
		$need = $shopValue->diamond *($shopValue->times + 1);
		// if($shopValue->isbuy)
		// {
			// $returnData->fail = 3;
			// break;
		// }
		
		if($userData->diamond < $need)
		{
			$returnData->fail = 4;
			$returnData->sync_diamond = $userData->diamond;
			break;
		}
		if($shopValue->id == 'coin')
			$userData->addCoin($shopValue->num);
		else if($shopValue->id == 'energy')
			$userData->addEnergy($shopValue->num);
		else
			$userData->addProp($shopValue->id,$shopValue->num);
		$userData->addDiamond(-$need);
		
		$arr[$shopKey]->times++;
		$sql = "update ".getSQLTable('shop')." set shop='".json_encode($arr)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);
		
		
	}while(false);
	
?> 