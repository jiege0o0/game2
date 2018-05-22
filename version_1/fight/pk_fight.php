<?php 
$id=$msg->id;
require_once($filePath."pk/pk_tool.php");


do{		
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 1;
		break;
	}
	
	$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);
	$info = json_decode($result['info']);
	$list = $info->card;
	
	//产生敌人
	if(!$info->enemy)
	{
		//计算关卡战力
		$force=1;
		$enemy = array();
		$enemy['force'] = $force;
		$enemy['list'] = ''.rand(1,3);
		$enemy['type'] = 1;
		$enemy['hp'] = $userData->getHp();
		
		
		$player = createNpcPlayer(2,2,$enemy);
		$nick = '关卡守卫'.$info->step;
		$player->nick = base64_encode($nick);
		
		$info->enemy = $player;
		
		$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);
	}
	
	
	$pkData = new stdClass();
	$pkData->seed = time();
	$pkData->players = array();
	array_push($pkData->players,createUserPlayer(1,1,$userData,$list));
	array_push($pkData->players,$info->enemy);

	
	
	$returnData -> pkdata = $pkData;
	$userData->addEnergy(-1);
	$userData->pk_common->pktype = 'fight';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->pkcard = $list;
	$userData->pk_common->level = $info->step;
	$userData->pk_common->time = time();
	$userData->setChangeKey('pk_common');
	
	

}while(false)


?> 