<?php 
require_once($filePath."cache/base.php");
$cost = 1;
$tecLevel = $userData->getTecLevel(1);
do{
	if($userData->getPropNum(102) < $cost)
	{
		$returnData -> fail = 1;
		break;
	}
	$haveSkill = array();
	$noSkill = array();
	foreach($skill_base as $key=>$value)
	{
		if($value['level'] <= $tecLevel)
		{
			if($value['level'] == 0 || in_array($value['id'],$userData->card->skill))
				array_push($haveSkill,$value['id']);
			else
				array_push($noSkill,$value['id']);
		}
	}
	$len1 = count($haveSkill);
	$len2 = count($noSkill);
	
	if($len1 == $len2)
	{
		$returnData -> fail = 2;
		break;
	}
	
	$earnProp = 0;
	if(rand(1,$len1 + $len2) <= $len2)//中
	{
		$index = rand(1,$len1 + $len2);
		if($index <= $len2)//中上中
			$id = $noSkill[$index-1];
		else
		{
			$id = $haveSkill[$index-$len2-1];
			$earnProp = 1;
		}
	}
	else
	{
		$id = $haveSkill[rand(0,$len1-1)];
		$earnProp = 1;
	}
	$userData->addProp(102,-$cost);
	if($earnProp)
	{
		$userData->addProp(103,$earnProp);
	}
	else
	{
		array_push($userData->card->skill,$id);
		$userData->setChangeKey('card');
	}
	
	$returnData -> id = $id;
	$returnData -> addprop = $earnProp;
	
}while(false)

?> 