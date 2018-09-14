<?php 
$list=$msg->list;

require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");

$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$info = json_decode($result['info']);
do{		
	if($userData->pk_common->pktype != 'fight')//最近不是打这个
	{
		$returnData -> fail = 1;
		break;
	}
	
	if($userData->pk_common->lastkey == $msg->key)
	{
		$lastData = $userData->pk_common->lastreturn;
		foreach($lastData as $key=>$value)
		{
			$returnData ->{$key} = $value;
		}
		break;
	}
	$userData->pk_common->lastkey = $msg->key;
	$userData->pk_common->lastreturn = $returnData;
	$userData->setChangeKey('pk_common');
	
	
	
	$pkData = $userData->pk_common->pkdata;
	
	$playerData = getUserPKData($list,$pkData->players[0],$msg->cd,$msg->key,$pkData->seed);
	$enempList = $pkData->players[1]->autolist;
	if($playerData -> fail)//出怪顺序有问题
	{
		$returnData -> fail = $playerData -> fail;
		break;
	}
	
	$force = $info->enemy->force;
	
	//减去手牌
	$card = explode(",",$info->card);
	foreach($playerData->list as $key=>$value)
	{
		if($value['mid'] < 500)
		{
			$index = array_search($value['mid'], $card);
			if($card[$index] == $value['mid'])
			{
				array_splice($card,$index,1);	
				$haveDelete = true;
			}				
		}
	}
	if(!$haveDelete)//最少去除一张
		array_shift($card);
	$info->card = join(",",$card);
	$info->index ++;
	$info->enemy = '';
	
	$award = $info->win_award;
	require($filePath."active/get_award.php");
	$returnData->award = $award;
	
	$awardNum = $info->index + 1;
	require($filePath."active/init_award.php");
	$returnData->win_award = $award;
	$info->win_award = $award;//奖励
	
	
	if($info->index >=12)
		$info->award = '';
	else
	{
		require_once($filePath."fight/get_award_card.php");
		$info->award = $getAwardCard;
	}
	
	
	$returnData->cardaward = $info->award;
	$returnData->card = $info->card;
	

	$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);

}while(false);



?> 