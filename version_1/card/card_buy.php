<?php 
require_once($filePath."cache/base.php");
$id = $msg->id;
$cost = 30;
do{
	if($userData->getPropNum(103) < $cost)
	{
		$returnData -> fail = 1;
		$returnData->sync_coin = $userData->coin;
		break;
	}
	
	if(in_array($id,$userData->card->skill))
	{
		$returnData -> fail = 2;
		break;
	}
	
	array_push($userData->card->skill,$id);
	$userData->addProp(103,-$cost);
	$userData->setChangeKey('card');
	$returnData -> id = $id;
}while(false)

?> 