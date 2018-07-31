<?php 
$id=$msg->id;
require_once($filePath."pk/pk_tool.php");

do{		
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 1;
		break;
	}
	
	$myScore = 0;
	$sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);
	$conne->close_rst();
	$offlineData = json_decode($result['offline']);
	if($offlineData->score)
		$myScore = $offlineData->score;
	$winNum = 0;
	if($offlineData->winnum)
		$winNum = $offlineData->winnum;
	$lastList = $offlineData->list;
	if(!$lastList)
		$lastList = array();
	
	$force1 = floor($myScore * (0.9 + $winNum/100));
	$force2 = floor($myScore * (1.1 + $winNum/100));
	if($force2 - $force1 < 50)
		$force2 += 50 - $force1;
	
	$sql = "select * from ".getSQLTable('pvp_offline')." where score between ".$force1." and ".$force2." and gameid!='".$msg->gameid."' ORDER BY time DESC limit 20";
	$result = $conne->getRowsArray($sql);
	$enemyData = array();
	if($result)
	{
		foreach($result as $key=>$value)
		{
			if(!in_array($value['gameid'],$lastList,true))
			{
				array_push($enemyData,$value);
			}
		}
		$enemy = array_rand($enemyData,1);
		if($enemy)
		{
			$enemy = json_decode($enemy);
		}
	}
	
	if(!$enemy)//没敌人就从关卡中找一个
	{
		require_once($filePath."cache/map2.php");
		$enemy = createNpcPlayer(2,2,$hang_base[rand(101,200)]);
		$enemy->nick = base64_encode('神秘人'）
	}
	$enemy->force = 500;
	
	
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
	
	

}while(false);

?> 