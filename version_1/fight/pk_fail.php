<?php 
$list=$msg->list;


$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$info = json_decode($result['info']);
do{		
	if($userData->pk_common->pktype != 'fight' || $userData->pk_common->level != $info->step)//最近不是打这个
	{
		$returnData -> fail = 1;
		break;
	}
	
	//减去手牌
	$card = explode(",",$info->card);
	$list = explode(",",$list);
	foreach($list as $key=>$value)
	{
		$group = explode("#",$list[$i]);
		$id = $group[1];
		if($id < 500)
		{
			$index = array_search($id, $card);
			array_splice($card,$index,1);		
		}	
	}
	$info->card = join(",",$card);
	$returnData->card = $info->card;

	$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);

}while(false);



?> 