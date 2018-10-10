<?php 
$enemy=$msg->enemy;
$seed=$msg->seed;
require_once($filePath."pk/pk_tool.php");
require_once($filePath."pvp/pvp_tool.php");

do{		
	deleteSkillCard($userData->pk_common->pkcard);
	$userData->addEnergy(-1);
	
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
	
	$userData->pk_common->pkdata->pkstarttime = time();
	$userData->pk_common->pkdata->enemy = $enemy;
	$userData->pk_common->pkdata->seed = $seed;
	$userData->setChangeKey('pk_common');


	$sql = "update ".getSQLTable('pvp')." set offline='".json_encode($offlineData)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
	
	

}while(false);

?> 