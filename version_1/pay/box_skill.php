<?php 
require_once($filePath."cache/base.php");
do{
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
	$skillID = $skillArr[0];
	$num = 30*$awardNum;
	$award->skills[$skillID] = $num;

	$userData->addSkill($skillID,$num);
	
	
	$returnData->award = $award;	
}while(false)
?> 