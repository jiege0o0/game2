<?php 
$id=$msg->id;
require_once($filePath."pk/pk_tool.php");
require_once($filePath."pvp/pvp_tool.php");

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
	$myLevel = getPVPLevel($myScore);
	$maxHeroLevel = min(5,ceil($myLevel/3));
	$winNum = 0;
	if($offlineData->cwin)
		$winNum = $offlineData->cwin;
		
	if(!$offlineData->pknum)
		$offlineData->pknum = 1;
	else
		$offlineData->pknum ++;
		
	$offlineData->list;
	if(!$offlineData->list)
		$offlineData->list = array();
		
	$preSubScore = min(20,$myScore);
	$offlineData->subscore = $preSubScore;
	$offlineData->score = $myScore - $preSubScore;
	
	$force1 = floor($myScore * (0.8 + $winNum/100));
	$force2 = floor($myScore * (1.2 + $winNum/100));
	if($force2 - $force1 < 50)
		$force2 += 50 - $force1;
	
	$sql = "select * from ".getSQLTable('pvp_offline')." where score between ".$force1." and ".$force2." and gameid!='".$msg->gameid."' ORDER BY time DESC limit 20";
	$result = $conne->getRowsArray($sql);
	// debug($sql);
	$enemyData = array();
	if($result)
	{
		foreach($result as $key=>$value)
		{
			if(!in_array($value['gameid'],$offlineData->list,true))
			{
				array_push($enemyData,$value['data']);
			}
		}
		$len = count($enemyData);
		if($len)
		{
			$enemyStr = $enemyData[rand(0,$len-1)];
			$enemy = json_decode($enemyStr);
			$enemy->team=2;
			$enemy->id=2;
			$otherLevel = getPVPLevel($enemy->score);
			$enemy->def = max(0,5 + ($myLevel - $otherLevel));
			
			array_push($offlineData->list,$enemy->gameid);
			while(count($offlineData->list) > 5)
				array_shift($offlineData->list);
		}
	}
	
	if(!$enemy)//没敌人就从关卡中找一个
	{
		$level = $userData->hang->level + rand(5,60);
		$mapIndex = ceil($level/100);
		require_once($filePath."cache/map".$mapIndex.".php");
		$enemy = createNpcPlayer(2,2,$hang_base[$level]);
		if($enemy->hero)
		{
			$enemy->hero = explode(",",$enemy->hero);
			$heroLevel = max(1,min($maxHeroLevel,floor(pow($level/100,0.8))));
			foreach($enemy->hero as $key=>$value)
			{
				$enemy->hero[$key] = $value.'|'.$heroLevel;
			}
			$enemy->hero = join(",",$enemy->hero);
		}
		$enemy->nick = base64_encode('神秘人');
		$enemy->head=0;
		$enemy->def = 5 + $myLevel;
	}
	$enemy->force = 1000;
	
	
	$pkData = new stdClass();
	$pkData->seed = time();
	$pkData->players = array();
	$pkData->check = true;
	
	foreach($userData->def_list->list as $key=>$value)
	{
		if($value->id == $id)
		{
			$list = $value->list;
			$hero = $value->hero;
			break;
		}
	}
	$userData->maxHeroLevel = $maxHeroLevel;
	$myPlayer = createUserPlayer(1,1,$userData,$list,$hero,true);
	$myPlayer->force = 1000;
	array_push($pkData->players,$myPlayer);
	array_push($pkData->players,$enemy);

	
	$returnData -> pkdata = $pkData;
	$userData->addEnergy(-1);
	$userData->pk_common->pktype = 'pvp_offline';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->time = time();
	$userData->setChangeKey('pk_common');
	

	$sql = "update ".getSQLTable('pvp')." set offline='".json_encode($offlineData)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
	
	

}while(false);

?> 