<?php 
$list=$msg->list;

require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");

$sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$task = json_decode($result['task']);
$offlineData = json_decode($result['offline']);

do{		
	if($userData->pk_common->pktype != 'pvp_online')//最近不是打这个
	{
		$returnData -> fail = 1;
		break;
	}
	if(!$userData->pk_common->pkdata->pkstarttime)//最近不是打这个
	{
		$returnData -> fail = 2;
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
	$userData->pk_common->pkdata->pkstarttime = 0;
	$userData->setChangeKey('pk_common');
	

	$pkData = $userData->pk_common->pkdata;
	if(!testPVPServerKey($msg->serverkey,$pkData->pkdata))//与PVP服务器返回的key对不上
	{
		$returnData -> fail = 110;
		break;
	}
	
	$playerData = getUserPKData($list,$pkData->pkdata,$msg->cd,$msg->key,$pkData->seed);
	backSkillCard($playerData->skill);
	if($playerData -> fail)//出怪顺序有问题
	{
		$returnData -> fail = $playerData -> fail;
		break;
	}
	$isPKWin = true;
	require_once($filePath."pvp/finish_task.php");

	$myScore = $offlineData->score;
	if(!$myScore)
		$myScore = 0;
	$myScore += $offlineData->subscore;
	$offlineData->subscore = 0;
	
	$award = new stdClass();
	$enemy = $pkData->enemy;
	
	if($myScore >= $enemy->score)
		$addScore = max(8,20 - floor(pow($myScore - $enemy->score,0.6)));
	else
		$addScore = 20 + ceil(pow($enemy->score - $myScore,0.6));

	$award->offline_value = $addScore;
	$award->coin = $addScore*80;
	$userData->addCoin($addCoin);
	
	
	$offlineData->score = floor($myScore + $addScore);
	if(!$offlineData->maxscore)
		$offlineData->maxscore = $offlineData->score;
	else
		$offlineData->maxscore = max($offlineData->score,$offlineData->maxscore);
		
	if(!$offlineData->cwin)
		$offlineData->cwin = 1;
	else if($offlineData->cwin < 0)
		$offlineData->cwin = 0;
	else 
		$offlineData->cwin++;
		
	if(!$offlineData->time)
		$offlineData->time = time();
		
	if(!$offlineData->winnum)
		$offlineData->winnum = 1;
	else
		$offlineData->winnum ++;
		
	
		
	
	
	
	
	$returnData->award = $award;
	$returnData->score = $offlineData->score;
	$returnData->task = $task;
	

	$sql = "update ".getSQLTable('pvp')." set task='".json_encode($task)."',offline='".json_encode($offlineData)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
	
	//更新列表
	$sql = "update ".getSQLTable('pvp_offline')." set score=".$offlineData->score." where gameid='".$userData->gameid."'";
	if(!$conne->uidRst($sql))
	{
		$sql = "insert into ".getSQLTable('pvp_offline')."(gameid,score,time) values('".$userData->gameid."',".$offlineData->score.",".time().")";
		$conne->uidRst($sql);
	}

}while(false);



?> 