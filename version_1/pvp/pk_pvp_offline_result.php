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
	$isPKWin = true;
	require_once($filePath."pvp/finish_task.php");

	$mySorce = $offlineData->sorce;
	if(!$mySorce)
		$mySorce = 0;
	
	$award = new stdClass();
	$enemy = $pkData->players[1];
	if($enemy->gameid == 'npc')//打电脑
	{
		$addSorce = 5;
	}
	else
	{
		if($mySorce >= $enemy->sorce)
			$addSorce = max(5,15 - pow($mySorce - $enemy->sorce,0.6));
		else
			$addSorce = 15 + pow($enemy->sorce - $mySorce,0.6);
	}
	$award->offline_value = $addS;
	$award->coin = $addSorce*50;
	$userData->addCoin($addCoin);
	
	
	$offlineData->sorce = $mySorce + $addSorce;
	if(!$offlineData->maxsorce)
		$offlineData->maxsorce = $offlineData->sorce;
	else
		$offlineData->maxsorce = max($offlineData->sorce,$offlineData->maxsorce);
		
	if(!$offlineData->winnum)
		$offlineData->winnum = 1;
	else if($offlineData->winnum < 0)
		$offlineData->winnum = 0;
	else 
		$offlineData->winnum++;
		
	if(!$offlineData->time)
		$offlineData->time = time();
		
	if(!$offlineData->list)
		$offlineData->list = array();
	if($enemy->gameid != 'npc')	
	{
		array_push($offlineData->list,$enemy->gameid);
		while(count($offlineData->list) > 5);
			array_shift($offlineData->list);
	}
	
		
	
	
	
	
	$returnData->award = $award;
	$returnData->sorce = $offlineData->sorce;
	$returnData->task = $task;
	

	$sql = "update ".getSQLTable('pvp')." set task='".json_encode($task)."',offline='".json_encode($offlineData)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
	
	//更新列表
	$myPlayer = $pkData->players[0];
	$myPlayer->score = $offlineData->sorce;
	$sql = "update ".getSQLTable('pvp_offline')." set data='".json_encode($myPlayer)."',score=".$offlineData->sorce.",time=".time()." where gameid='".$userData->gameid."'";
	if(!$conne->uidRst($sql))
	{
		$sql = "insert into ".getSQLTable('pvp_offline')."(gameid,data,score,time) values('".$userData->gameid."','".json_encode($myPlayer)."',".$offlineData->sorce.",".time().")";
		$conne->uidRst($sql);
	}

}while(false);



?> 