<?php 
require_once($filePath."cache/base.php");
do{
	if(!$award)
		$award = new stdClass();
	$award->skills = array();

	$tecLevel = $userData->level;
	$skillArr = array();
	foreach($skill_base as $key=>$value)
	{
		if($value['level'] <= $tecLevel && $userData->getSkill($key) < 999)//@skill
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
		if($awardSkillNumOnce)
			$num = $awardSkillNumOnce;
		else
			$num = 30;
		if($award->skills[$skillID])
			$award->skills[$skillID] += $num;
		else
			$award->skills[$skillID] = $num;
		$awardNum--;
		$userData->addSkill($skillID,$num);
	}

	$returnData->award = $award;	
}while(false)
?> 