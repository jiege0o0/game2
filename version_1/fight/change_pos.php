<?php 
$list=$msg->list;
$hero=$msg->hero;

$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$info = json_decode($result['info']);


$oldCard = explode(",",$info->card);
$newCard = explode(",",$list);

$oldHero = explode(",",$info->hero);
$newHero = explode(",",$hero);

do{
	if(sort($oldCard) != sort($newCard))
	{
		$returnData -> fail = 1;
		break;
	}
	if(sort($oldHero) != sort($newHero))
	{
		$returnData -> fail = 2;
		break;
	}
	$info->card = $list;
	$info->hero = $hero;
	$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
	
}while(false)


?> 