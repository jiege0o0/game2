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
			if($value->key == $id)
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
		$need = floor($shopValue->diamond *($shopValue->times*0.2 + 1));
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
		$award = new stdClass();
		$award->props = array();
		if($shopValue->id == 'coin')
		{
			$userData->addCoin($shopValue->num);
			$award->coin = $shopValue->num;
			$returnData->award = $award;
		}
		else if($shopValue->id == 'energy')
		{
			$userData->addEnergy($shopValue->num);
			$award->energy = $shopValue->num;
			$returnData->award = $award;
		}
		else if($shopValue->id == 'box_resource')
		{
			$awardNum = $shopValue->num;
			require_once($filePath."pay/box_resource.php");
		}
		else if($shopValue->id == 'box_skill')
		{
			$awardNum = $shopValue->num;
			require_once($filePath."pay/box_skill.php");
		}
		else if($shopValue->id == 'box_hero')
		{
			$awardNum = $shopValue->num;
			require_once($filePath."pay/box_hero.php");
		}
		else if(substr($shopValue->id,0,5) == 'skill')
		{
			// $award->skills = array();
			// $award->skills[substr($shopValue->id,5)] = $shopValue->num;
			$userData->addSkill(substr($shopValue->id,5),$shopValue->num);
		}
		else
		{
			$userData->addProp($shopValue->id,$shopValue->num);
			$award->props->{$shopValue->id} = $shopValue->num;
			$returnData->award = $award;
		}
		$userData->addDiamond(-$need);
		
		$arr[$shopKey]->times++;
		$sql = "update ".getSQLTable('shop')." set shop='".json_encode($arr)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);
		
		
	}while(false);
	
?> 