<?php 
$id=$msg->id;
require_once($filePath."pk/pk_tool.php");

do{		
	if($userData->pk_common->pktype != 'pvp_offline')//最近不是打这个
	{
		$returnData -> fail = 2;
		break;
	}
	if($userData->pk_common->pkdata->seed != $msg->seed)//最近不是打这个
	{
		$returnData -> fail = 4;
		break;
	}
	
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 1;
		break;
	}
	
	$pkData = $userData->pk_common->pkdata;
	$enemy = $pkData->players[1];
	
	
	$pkData = new stdClass();
	$pkData->seed = time();
	$pkData->players = array();
	$pkData->check = true;
	
	foreach($userData->def_list->list as $key=>$value)
	{
		if($value->id == $id)
		{
			$list = $value->list;
			break;
		}
	}
	$myPlayer = createUserPlayer(1,1,$userData,$list,true);
	$myPlayer->force = 500;
	array_push($pkData->players,$myPlayer);
	array_push($pkData->players,$enemy);

	
	$returnData -> pkdata = $pkData;
	$userData->addEnergy(-1);
	$userData->pk_common->pktype = 'pvp_offline';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->time = time();
	$userData->setChangeKey('pk_common');
	
	
	$myScore = 0;
	$sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);
	$conne->close_rst();
	$offlineData = json_decode($result['offline']);
	
	if($offlineData->score)
		$myScore = $offlineData->score;
	$preSubScore = min(30,$myScore);
	$offlineData->subscore = $preSubScore;
	$offlineData->score = $myScore - $preSubScore;
	
	
	$sql = "update ".getSQLTable('pvp')." set offline='".json_encode($offlineData)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
	
	

}while(false);

?> 