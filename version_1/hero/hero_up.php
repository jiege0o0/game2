<?php 
$heroID = $msg->id;
do{
	$curLV = $userData->getHeroLevel($heroID);
	$nextLV = $userData->getMaxHeroLevel($heroID);
	if($curLV >= $nextLV)
	{
		$returnData->fail = 1;
		break;
	}
	$need = $curLV;
	if($userData->getPropNum(101) < $need)
	{
		$returnData->fail = 2;
		break;
	}
	
	$userData->addProp(101,-$need);
	$userData->addHeroLV($heroID);
}while(false)
?> 