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
	if(!testSkillCard($list))//技能卡数量不足
	{
		$returnData -> fail = 3;
		break;
	}
	
	$myScore = 0;
	$sql = "select * from ".getSQLTable('pvp')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);
	$conne->close_rst();
	$offlineData = json_decode($result['offline']);
	if($offlineData->score)
		$myScore = $offlineData->score;
	// $myLevel = getPVPLevel($myScore);
	// $maxHeroLevel = min(5,ceil($myLevel/3));
	// $winNum = 0;
	// if($offlineData->cwin)
		// $winNum = $offlineData->cwin;
		
	// if(!$offlineData->pknum)
		// $offlineData->pknum = 1;
	// else
		// $offlineData->pknum ++;
		
	// $offlineData->list;
	// if(!$offlineData->list)
		// $offlineData->list = array();
		
	// $preSubScore = min(20,$myScore);
	// $offlineData->subscore = $preSubScore;
	// $offlineData->score = $myScore - $preSubScore;
	
	
	
	
	
	
	
	foreach($userData->atk_list->list as $key=>$value)
	{
		if($value->id == $id)
		{
			$list = $value->list;
			$hero = $value->hero;
			break;
		}
	}
	$userData->maxHeroLevel = $maxHeroLevel;
	$myPlayer = createUserPlayer(1,1,$userData,$list,$hero);
	$myPlayer->force = 1000;
	
	$pkData = new stdClass();
	$pkData->source = $myScore;
	$pkData->gameid = $userData->gameid;
	$pkData->pktype = 'pvp';
	$pkData->pkdata = $myPlayer;


	
	$returnData -> pkdata = $pkData;
	$userData->pk_common->pktype = 'pvp_online';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->time = time();
	$userData->pk_common->pkcard = $list;
	$userData->pk_common->hero = $hero;
	$userData->pk_common->pkstarttime = 0;
	$userData->setChangeKey('pk_common');
	

	// $sql = "update ".getSQLTable('pvp')." set offline='".json_encode($offlineData)."' where gameid='".$userData->gameid."'";
	// $conne->uidRst($sql);
	
	

}while(false);

?> 