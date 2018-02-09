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
		if($shopValue->num == 0)
		{
			$returnData->fail = 3;
			break;
		}
		
		if($userData->diamond < $shopValue->diamond)
		{
			$returnData->fail = 4;
			$returnData->sync_diamond = $userData->diamond;
			break;
		}
		if($shopValue->id == 'coin')
			$userData->addCoin($shopValue->num);
		else
			$userData->addProp($shopValue->id,$shopValue->num);
		$userData->addDiamond(-$shopValue->diamond);
		
		$arr[$shopKey]->num = 0;
		$sql = "update ".getSQLTable('shop')." set shop='".json_encode($arr)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);
		
		
	}while(false);
	
?> 