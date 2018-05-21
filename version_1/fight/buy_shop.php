<?php 
	$id = $msg->id;
	do{
		$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
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
		if($shopValue->isbuy)
		{
			$returnData->fail = 3;
			break;
		}
		
		$info = json_decode($result['info']);
		if($info->value < $shopValue->diamond)
		{
			$returnData->fail = 4;
			$returnData->value = $info->value;
			break;
		}
		if($shopValue->id == 'coin')
			$userData->addCoin($shopValue->num);
		else if($shopValue->id == 'energy')
			$userData->addEnergy($shopValue->num);
		else if(substr($shopValue->id,0,5) == 'skill')
			$userData->addSkill(substr($shopValue->id,5),$shopValue->num);
		else
			$userData->addProp($shopValue->id,$shopValue->num);
			
		$info->value -=$shopValue->diamond;
		
		$arr[$shopKey]->isbuy = true;
		$sql = "update ".getSQLTable('fight')." set shop='".json_encode($arr)."',info='".json_encode($info)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);
		
		
	}while(false);
	
?> 