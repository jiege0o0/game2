<?php 
require_once($filePath."cache/base.php"); 
do{
	$level = $userData->hang->level;
	$award = new stdClass();
	$coinCD = 10;
	$coinLevel = 0;
	$awardCD = 3600 * $awardNum;
	for($i=1;$i<=22;$i++)
	{
		$coinLevel += $userData->getTecLevel(300 + $i);
	}
	$addCoin = floor($awardCD/$coinCD*0.3*pow($level,0.8)*(1+$coinLevel*0.002)) + $userData->hourcoin*$awardNum;
	$award->coin = $addCoin;
	$award->skills = array();
	$award->props = array();
	
	if($awardNum <=2)
		break;
	
	$num = $awardNum > 6?2:1;
	while($num)
	{
		$num --;
		$rd = rand(1,100);
		
		if($rd < 10 && !$award->diamond)
		{
			$award->diamond = $awardNum;
			continue;
		}
		
		if($rd < 15 && !$award->props[101] && $awardNum > 10)
		{
			$award->props[101] = 1;
			continue;
		}
	
		if($rd < 25)//¼¼ÄÜ
		{
			if(!$skillArr)
			{
				$tecLevel = $userData->level;
				$skillArr = array();
				foreach($skill_base as $key=>$value)
				{
					if($value['level'] <= $tecLevel && $userData->getSkill($key) < 999)//@skill
					{
						array_push($skillArr,$key);
					}
				}
				if($skillArr[1])
				{
					usort($skillArr,randomSortFun);
				}
			}
			
			if($skillArr[0])
			{
				$award->skills[$skillArr[0]] = floor($awardNum*1.5);
				array_shift($skillArr);
				continue;
			}	
		}
		
		if($rd < 40 && !$award->energy)
		{
			$award->energy = $awardNum;
			continue;
		}
		
		if(!$propsArr)
		{
			$propsArr = array();
			foreach($prop_base as $key=>$value)
			{
				if($value['hanglevel'] && ($value['hanglevel']<=$level || $value['droplevel'] <= $userData->level))
				{
					$propCD = getPropCD($level,$value['hanglevel'],$userData->getTecLevel(300 + $key));
					if($propCD)
					{
						$addProp = ceil($awardCD/$propCD/2);
						array_push($propsArr,array('id'=>$key,'num'=>$addProp));
					}
				}
			}
			usort($propsArr,randomSortFun);
		}
		$award->props[$propsArr[0]['id']] = floor($awardNum*0.6*$propsArr[0]['num']);
		array_shift($propsArr);
	}
}while(false);

function getPropCD($clv,$slv,$tlv){
	$hourEarn = (floor(min(max(1,$clv-$slv),100)/10) + 1)*(1 + $tlv*5/100);
	if($hourEarn <= 0)
		return 0;
	return 3600/$hourEarn;
}

?> 