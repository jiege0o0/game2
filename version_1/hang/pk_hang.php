<?php 
$id=$msg->id;
$hangIndex=$userData->hang->level + 1;
$mapIndex = ceil($hangIndex/100);
require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/map".$mapIndex.".php");


do{		
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 1;
		break;
	}
	
	foreach($userData->atk_list->list as $key=>$value)
	{
		if($value->id == $id)
		{
			$list = $value->list;
			break;
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
	//if($hangIndex > 20)
		recordPKData('hang',$list);
	$hang_base[$hangIndex]['force']=$force;
	array_push($pkData->players,createUserPlayer(1,1,$userData,$list));
	$player = createNpcPlayer(2,2,$hang_base[$hangIndex]);
	$nick = '战役守卫'.$hangIndex;
	$player->nick = base64_encode($nick);
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