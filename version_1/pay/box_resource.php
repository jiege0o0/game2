<?php 
require_once($filePath."cache/base.php");
//当前等级下，产出单位个的间隔
function getPropCD($clv,$slv,$tlv){
	$hourEarn = (floor(min(max(1,$clv-$slv),100)/10) + 1)*(1 + $tlv*5/100);
	if($hourEarn <= 0)
		return 0;
	return 3600/$hourEarn;
}

function getCDByIndex($data,$index){
	return(int)$data[$index];
}


do{
	$level = $userData->hang->level;
	// $awardNum = 1;
	$awardCD = 3600 * $awardNum;
	if(!$award)
		$award = new stdClass();
	$award->props = array();
	
	$coinCD = 10;//3600/(90+$level*10 + floor($level/5)*20);
	$coinLevel = 0;
	for($i=1;$i<=22;$i++)
	{
		$coinLevel += $userData->getTecLevel(300 + $i);
	}
	$addCoin = floor($awardCD/$coinCD*0.3*pow($level,0.8)*(1+$coinLevel*0.002)) + $userData->hourcoin*$awardNum;
	$userData->addCoin($addCoin);
	$award->coin = $addCoin;
	
	$maxPropID = 0;
	foreach($prop_base as $key=>$value)
	{
		if($value['hanglevel'] && ($value['hanglevel']<=$level || $value['droplevel'] <= $userData->level))
		{
			$propCD = getPropCD($level,$value['hanglevel'],$userData->getTecLevel(300 + $key));
			if($propCD)
			{
				$maxPropID = max($maxPropID,$key);
				$addProp = floor($awardCD/$propCD);
				$award->props[$key] = $addProp;
				$userData->addProp($key,$addProp);
			}
		}
	}
	$returnData->award = $award;	
}while(false)
?> 