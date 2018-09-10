<?php 
$list=$msg->list;

require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");

do{		
	if($userData->pk_common->pktype != 'random')//最近不是打这个
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
	$pkData->players[0]->isRandom = true;
	$playerData = getUserPKData($list,$pkData->players[0],$msg->cd,$msg->key,$pkData->seed);
	$enempList = $pkData->players[1]->autolist;
	if($playerData -> fail)//出怪顺序有问题
	{
		$returnData -> fail = $playerData -> fail;
		break;
	}
	
	$sql = "select * from ".getSQLTable('random')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);	
	$info = json_decode($result['info']);
	$info->num++;
	$info->index++;
	
	$award = $info->win_award;
	require($filePath."active/get_award.php");
	$returnData->award = $award;
	
	$awardNum = $info->index + 1;
	require($filePath."active/init_award.php");
	$returnData->win_award = $award;
	$info->win_award = $award;//奖励
	
	unset($info->pkData);
	
	$sql = "update ".getSQLTable('random')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
}while(false);



?> 