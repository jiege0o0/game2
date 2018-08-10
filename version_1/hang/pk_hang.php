<?php 
$id=$msg->id;
$hangIndex=$userData->hang->level + 1;
$mapIndex = ceil($hangIndex/100);
require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/map".$mapIndex.".php");
$isAuto = $msg->isauto;

do{		
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 1;
		break;
	}
	
	if($isAuto)
	{
		foreach($userData->def_list->list as $key=>$value)
		{
			if($value->id == $id)
			{
				$list = $value->list;
				$hero = $value->hero;
				break;
			}
		}
	}
	else
	{
		foreach($userData->atk_list->list as $key=>$value)
	{
		if($value->id == $id)
		{
			$list = $value->list;
			$hero = $value->hero;
			break;
		}
	}
	}
	
	

	if(!$list)
	{
		$returnData -> fail = 2;
		break;
	}
	
	$pkData = new stdClass();
	$pkData->seed = time();
	$pkData->players = array();
	$pkData->check = true;
	
	//计算关卡战力
	$force=1;
	for($i=1;$i<$hangIndex;$i++)
	{	
		$force+=floor($i/10+1);
	}
	
	if(!deleteSkillCard($list))//技能卡数量不足
	{
		$returnData -> fail = 3;
		break;
	}
	if($hangIndex > 20)
		recordPKData('hang',$list,$isAuto?'a':'');
	$hang_base[$hangIndex]['force']=$force;
	array_push($pkData->players,createUserPlayer(1,1,$userData,$list,$hero,$isAuto));
	$player = createNpcPlayer(2,2,$hang_base[$hangIndex]);
	$nick = '战役守卫'.$hangIndex;
	$player->nick = base64_encode($nick);
	
	
	$sql = "select gameid,tec_force from ".getSQLTable('slave')." where gameid=(select master from ".getSQLTable('slave')." where gameid='".$userData->gameid."')";
	$result = $conne->getRowsRst($sql);
	if($result && $result['gameid'] != $userData->gameid)
	{
		if((int)$result['tec_force'] > $userData->tec_force)
		$pkData->players[0]->force += ceil(((int)$result['tec_force'] - $userData->tec_force)*0.05);
	}
	
	
	//战力上限
	$maxDef = floor($hangIndex/5);
	if($maxDef < $player->def)
		$player->def = $maxDef;
	array_push($pkData->players,$player);
	
	$returnData -> pkdata = $pkData;
	$userData->addEnergy(-1);
	$userData->pk_common->pktype = 'hang';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->pkcard = $list;
	$userData->pk_common->level = $hangIndex;
	$userData->pk_common->time = time();
	$userData->setChangeKey('pk_common');

}while(false)


?> 