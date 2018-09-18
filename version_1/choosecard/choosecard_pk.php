<?php 
$index=$msg->index;
require_once($filePath."pk/pk_tool.php");
do{		
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 1;
		break;
	}
	
	$sql = "select * from ".getSQLTable('choose')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);
	if(!$result)
	{
		$returnData -> fail = 2;
		break;
	}
	
	$info = json_decode($result['info']);
	if($info->num <=0)
	{
		$returnData -> fail = 3;
		break;
	}
	
	if($info->index != $index-1)
	{
		$returnData -> fail = 4;
		break;
	}

	if(!$info->enemy)
	{
		$begin = min(max($userData->level - 2,2),16);
		$level = $begin + ceil($index/3);
		require_once($dataFilePath."active/active_".$level.".php");
		$answerData = $pkActiveBase[rand(0,29)];
		$list = explode("|",$answerData);
		$card = $list[0];
			
		$enemyData=array('force'=>800 + $index*35,'type'=>0,'list'=>$card,'hp'=>3);	
		$enemy = createNpcPlayer(2,2,$enemyData);
		$enemy->def = 0;
		$enemy->nick = base64_encode('选卡'.$index);
		
		
	}
	else
	{
		$enemy = $info->enemy;
	}
	
	$myPlayer = createUserPlayer(1,1,$userData,$info->cardlist);
	$myPlayer->force = 1000;
	$myPlayer->hp = 3;
	
	
	$pkData = new stdClass();
	$pkData->seed = time();
	$pkData->players = array();
	$pkData->check = true;
	array_push($pkData->players,$myPlayer);
	array_push($pkData->players,$enemy);
	
	$info->enemy = $enemy;
		
	
	$returnData -> pkdata = $pkData;
	$userData->addEnergy(-1);
	$userData->pk_common->pktype = 'choose';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->time = time();
	$userData->setChangeKey('pk_common');
	

	$info->num --;
	$sql = "update ".getSQLTable('choose')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);

}while(false);

?> 