<?php 
$card = $msg->list;
$heroBase = $msg->hero;
$list = explode(",",$card);
$hero = explode(",",$heroBase);
require_once($filePath."pk/pk_tool.php");


$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$info = json_decode($result['info']);
// if(isSameDate($result['time']))
// {
	// $info->num = 1;
// }

do{		
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 11;
		break;
	}
	
	// if($msg->diamond)
	// {
		// if($userData->diamond<100)//没钻石
		// {
			// $returnData -> fail = 12;
			// $returnData->sync_diamond = $userData->diamond;
			// break;
		// }
		// else
			// $userData->addDiamond(-100);
	// }
	// else
	// {
		// if($info->num <= 0)//没次数
		// {
			// $returnData -> fail = 13;
			// break;
		// }
		// $info->num--; 
	// }
	
	require_once($filePath."pos/test_list.php");
	if($returnData -> fail)
		break;
		
	if(!deleteSkillCard($card))//技能卡数量不足
	{
		$returnData -> fail = 14;
		break;
	}
	if($userData->hang->level > 30)
		recordPKData('fight_init',$card);
	
	$info->card = $card;
	$info->hero = $heroBase;
	$info->init = true;
	$info->enemy = '';//当前敌人
	$info->award = '';//待选列表
	
	$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
	

}while(false)


?> 