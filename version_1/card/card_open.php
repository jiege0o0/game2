<?php 
require_once($filePath."cache/base.php");
$id = $userData->gameid;
$coin = floor(pow($monster_base[$id]['level'],3.05)*100-2000);
do{
	if($userData->getCoin() < $coin)
	{
		$returnData -> fail = 1;
		$returnData->sync_coin = $userData->coin;
		break;
	}
	
	if(in_array($id,$userData->card->monster))
	{
		$returnData -> fail = 2;
		break;
	}
	
	array_push($userData->card->monster,$id);
	$userData->addCoin(-$coin);
	$userData->setChangeKey('card');
	$returnData -> id = $id;
	
}while(false)

?> 