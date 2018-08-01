<?php 
	require_once($filePath."cache/base.php");
	$id = $msg->id;
	do{
		$sql = "select * from ".getSQLTable('prop_shop')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if(!$result || !isSameDate($result['time']))
		{
			$returnData->fail = 1;
			break;
		}
		$arr = json_decode($result['shop']);
		if($result['shop_base'])
			$base = json_decode($result['shop_base']);
		else
			$base = new stdClass();
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
		
		if($shopValue->isbuy)
		{
			$returnData->fail = 3;
			break;
		}
		if(!$base->{$shopValue->id})
			$base->{$shopValue->id} = 1000 + $prop_base[$shopValue->id]['hanglevel'];
		if($shopValue->type == 1)
		{
			if($userData->getPropNum($shopValue->id) < $shopValue->num)//µÀ¾ß²»×ã
			{
				$returnData->fail = 4;
				$returnData->sync_prop = new stdClass();
				$returnData->sync_prop->{$value['id']} = $userData->getPropNum($value['id']);
				break;
			}
			$userData->addProp($shopValue->id,-$shopValue->num);
			$userData->addCoin($shopValue->diamond);
			$base->{$shopValue->id} -= $shopValue->num/50;
		}
		else
		{
			if($userData->getCoin() < $shopValue->diamond)
			{
				$returnData->fail = 5;
				$returnData->sync_coin = $userData->coin;
				break;
			}
			$userData->addProp($shopValue->id,$shopValue->num);
			$userData->addCoin(-$shopValue->diamond);
			$base->{$shopValue->id} += $shopValue->num/50;
		}
		
		$shopValue->isbuy = true;
		$sql = "update ".getSQLTable('prop_shop')." set shop='".json_encode($arr)."',shop_base='".json_encode($base)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);
		debug($sql);
		
		
	}while(false);
	
?> 