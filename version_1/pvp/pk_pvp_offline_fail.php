<?php 
$list=$msg->list;

require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");

$sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$task = json_decode($result['task']);
$offlineData = json_decode($result['offline']);

do{		
	if($userData->pk_common->pktype != 'pvp_offline')//最近不是打这个
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
	$isPKWin = false;
	require_once($filePath."pvp/finish_task.php");

	$myScore = $offlineData->score;
	if(!$myScore)
		$myScore = 0;
	$myScore += $offlineData->subscore;
	$offlineData->subscore = 0;
	
	$award = new stdClass();
	$enemy = $pkData->players[1];
	if($enemy->gameid == 'npc')//打电脑
	{
		$addScore = -3;
	}
	else
	{
		if($myScore < $enemy->score)
			$addScore = -max(5,15 - ceil(pow($enemy->score - $myScore,0.6)));
		else
			$addScore = -(15 + floor(pow($myScore - $enemy->score,0.6)));
	}
	if($myScore < 3000)
		$addScore = floor($addScore*(7000+$myScore/3000)/10000);
	if(-$addScore > $myScore)
		$addScore = -$myScore;
		
	$award->offline_value = $addScore;
	
	
	$offlineData->score = floor(max(0,$myScore + $addScore));
		
	if(!$offlineData->cwin)
		$offlineData->cwin = -1;
	else if($offlineData->cwin < 0)
		$offlineData->cwin --;
	else 
		$offlineData->cwin = 0;
		
	if(!$offlineData->time)
		$offlineData->time = time();
		
	
	$returnData->award = $award;
	$returnData->score = $offlineData->score;
	$returnData->task = $task;
	

	$sql = "update ".getSQLTable('pvp')." set task='".json_encode($task)."',offline='".json_encode($offlineData)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
	
	//更新列表
	$myPlayer = $pkData->players[0];
	$myPlayer->score = $offlineData->score;
	$myPlayer->level = $userData->level;
	$sql = "update ".getSQLTable('pvp_offline')." set data='".json_encode($myPlayer)."',score=".$offlineData->score.",time=".time()." where gameid='".$userData->gameid."'";
	if(!$conne->uidRst($sql))
	{
		$sql = "insert into ".getSQLTable('pvp_offline')."(gameid,data,score,time) values('".$userData->gameid."','".json_encode($myPlayer)."',".$offlineData->score.",".time().")";
		$conne->uidRst($sql);
	}

}while(false);



?> 