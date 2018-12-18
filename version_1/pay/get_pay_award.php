<?php 
require_once($filePath."cache/base.php");
do{
	$step = floor($userData->rmb/30);
	if(!$step)
	{
		$returnData -> fail = 1;
		break;
	}
	$current = $userData->active->rmb_award;
	if(!$current)
		$current = 0;
	if($current >= $step)
	{
		$returnData -> fail = 2;
		break;
	}
	if(!$userData->active->rmb_award)
		$userData->active->rmb_award = 1;
	else
		$userData->active->rmb_award ++;
	$userData->setChangeKey('active');
	$type = rand(1,8);
	$award = new stdClass();
	
	$tecLevel = $userData->level;
	$skillAble = false;
	foreach($skill_base as $key=>$value)
	{
		if($value['level'] <= $tecLevel && $userData->getSkill($key) < 999)//@skill
		{
			$skillAble = true;
			break;
		}
	}
		
	$heroAble = false;
	foreach($monster_base as $key=>$value)
	{
		if($value['id'] > 100 && $value['id'] < 130 && $value['level']-1000 <= $tecLevel && $userData->getMaxHeroLevel($key) < 5)//@hero
		{
			$heroAble = true;
			break;
		}
	}
		
	if($type <= 3)
	{
		$awardNum = 24;
		require_once($filePath."pay/box_resource.php");
	}
	else if($type <= 5 && $skillAble)
	{
		$awardNum = 2;
		require_once($filePath."pay/box_skill.php");
	}
	else if($type <= 7 && $heroAble)
	{
		$awardNum = 2;
		require_once($filePath."pay/box_hero.php");
	}	
	else 
	{
		$award->props = array();
		$userData->addProp(101,5);
		$award->props[101] = 5;
		$returnData->award = $award;
	}		
	}while(false);
?>