<?php 
$list=$msg->list;

$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$info = json_decode($result['info']);


$oldCard = explode(",",$info->card);
$newCard = explode(",",$list);

do{
	if(sort($oldCard) != sort($newCard))
	{
		$returnData -> fail = 1;
		break;
	}
	$info->card = $list;
	$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
	
}while(false)


?> 