<?php 
require_once($filePath."cache/base.php");
//²¹³ä¿¨×é
	$skillNum = rand(3,7);
	$tecLevel = min($userData->tec_force/10,950);
	$skillArr = array();
	$awardSkillArr = array();
	foreach($skill_base as $key=>$value)
	{
		if($value['level'] <= $tecLevel)
		{
			array_push($skillArr,$value['id']);
			if($userData->getSkill($value['id']) < 999)//½±ÀøÓÃµÄ
				array_push($awardSkillArr,$value['id']);
		}
	}
	
	usort($skillArr,randomSortFun);
	$skillArr = array_slice($skillArr,0,$skillNum);
	
	
	$tecLevel = $userData->getTecLevel(1);
	$monsterArr = array();
	foreach($monster_base as $key=>$value)
	{
		if($value['level'] <= $tecLevel)
		{
			array_push($monsterArr,$value['id']);
			array_push($monsterArr,$value['id']);
		}
	}
	usort($monsterArr,randomSortFun);
	$monsterArr = array_slice($monsterArr,0,9-$skillNum);
	
	
	$getAwardCard = join(",",$monsterArr).','.join(",",$skillArr);
?> 