<?php 
require_once($filePath."cache/base.php");
do{
	if(!$award)
		$award = new stdClass();
	$award->hero = array();

	$tecLevel = $userData->level;
	$skillArr = array();
	foreach($monster_base as $key=>$value)
	{
		if($value['id'] > 100 && $value['id'] < 130 && $value['level']-1000 <= $tecLevel && $userData->getHeroLevel($key) < 5)//@skill
		{
			array_push($skillArr,$key);
		}
	}
	
	if(!$skillArr[0])
	{
		$returnData -> fail = 101;
		break;
	}
	
	usort($skillArr,randomSortFun);
	while($awardNum > 0)
	{
		$skillID = array_shift($skillArr);
		array_push($skillArr,$skillID);
		$num = 1;
		if($award->hero[$skillID])
			$award->hero[$skillID] += $num;
		else
			$award->hero[$skillID] = $num;
		$awardNum--;
		$userData->addHero($skillID,$num);
	}
	


	
	
	$returnData->award = $award;	
}while(false)
?> 