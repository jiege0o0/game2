<?php 
require_once($filePath."cache/base.php");
//当前等级下，产出单位个的间隔
function getPropCD($clv,$slv,$tlv){
	$hourEarn = ($clv-$slv + 1)*(1 + $tlv*5/100);
	if($hourEarn <= 0)
		return 0;
	return 3600/$hourEarn;
}

function getCDByIndex($data,$index){
	return(int)$data[$index];
}


do{
	if($userData->hang->cd)
		$awardCD = explode(",",$userData->hang->cd);
	else
		$awardCD = array();
	$level = $userData->hang->level;
	$lastTime = $userData->hang->awardtime;
	$cd = min(3600*10,time()- $lastTime);//离上次结算经过的时间
	if($cd<60)//未到领奖时间
	{
		$returnData -> fail = 1;
		break;
	}
	$award = new stdClass();
	$award->props = array();
	
	$coinCD = 3600/(90+$level*10 + floor($level/5)*20);
	$lastCoinCD = getCDByIndex($awardCD,0);
	if($lastCoinCD)
		$lastCoinCD += $cd;
	else
		$lastCoinCD = $cd;
	$addCoin = floor($lastCoinCD/$coinCD);
	$awardCD[0] = floor($lastCoinCD)%$coinCD + 1;
	$userData->addCoin($addCoin);
	$award->coin = $addCoin;
	
	$maxPropID = 0;
	foreach($prop_base as $key=>$value)
	{
		if($value['hanglevel'] && $value['hanglevel']<=$level)
		{
			$propCD = getPropCD($level,$value['hanglevel'],$userData->getTecLevel(300 + $key));
			if($propCD)
			{
				$maxPropID = max($maxPropID,$key);
				$lastCD = getCDByIndex($awardCD,$value['id']);
				if($lastCD)
					$lastCD += $cd;
				else
					$lastCD = $cd;
				
				$addProp = floor($lastCD/$propCD);

				
				if($addProp)
				{
					$awardCD[$value['id']] = floor($lastCD)%$propCD + 1;	
					$award->props[$key] = $addProp;
					$userData->addProp($key,$addProp);
				}
				else
				{
					$awardCD[$value['id']] = $lastCD;
				}
			}
		}
	}
	
	
	$userData->hang->awardtime = time();
	for($i=0;$i<=$maxPropID;$i++)
	{
		if(!$awardCD[$i])
			$awardCD[$i] = 1;
	}
	$userData->hang->cd = join(",",$awardCD);
	$userData->setChangeKey('hang');
	
	$returnData->award = $award;
	$returnData->awardtime = $userData->hang->awardtime;
	
}while(false)
?> 