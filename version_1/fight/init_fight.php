<?php 
$card = $msg->list;
$list = explode(",",$card);



$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);


$info = json_decode($result['info']);
if(isSameDate($result['time']))
{
	$info->num = 1;
}

do{		
	
	
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 11;
		break;
	}
	
	if($msg->diamond)
	{
		if($userData->diamond<100)//没钻石
		{
			$returnData -> fail = 12;
			$returnData->sync_diamond = $userData->diamond;
			break;
		}
		else
			$userData->addDiamond(-100)
	}
	else
	{
		if($info->num <= 0)//没次数
		{
			$returnData -> fail = 13;
			break;
		}
		$info->num--; 
	}
	
	require_once($filePath."pos/test_list.php");
	if($returnData -> fail)
		break;
	
	$info->card = $card;
	$info->level = max(0,$info->maxlevel - 5);
	$info->step = 0;//当前步骤
	$info->enemy = '';//当前敌人
	$info->award = '';//待选列表
	
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