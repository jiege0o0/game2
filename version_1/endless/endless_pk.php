<?php 
$id=$msg->id;
$index=$msg->index;
require_once($filePath."pk/pk_tool.php");
do{		
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 1;
		break;
	}
	// $returnData->question=$question;
	
	$sql = "select * from ".getSQLTable('endless')." where gameid='".$userData->gameid."'";
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
	
	if(!deleteSkillCard($list))//技能卡数量不足
	{
		$returnData -> fail = 5;
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
		$hero = $list[1];
		if($level >= 6)
			$heroLevel = floor(($level-3)/3);
		else
			$heroLevel = 0;
		if($hero)
		{
			$hero = explode(",",$hero);
			$len = count($hero);
			for($i=0;$i<$len;$i++)
			{	
				$hero[$i] =$hero[$i].'|'.$heroLevel;
			}
			$hero = join(",",$hero);
		}
			
		$enemyData=array('force'=>ceil($userData->tec_force*(1+$index/24)),'type'=>0,'list'=>$card,'hp'=>99,'hero'=>$hero);	
		$enemy = createNpcPlayer(2,2,$enemyData);
		$enemy->def = 0;
		$enemy->nick = base64_encode('无尽'.$index);
	}
	else
	{
		$enemy = $info->enemy;
	}
	
	
	
	foreach($userData->atk_list->list as $key=>$value)
	{
		if($value->id == $id)
		{
			$list = $value->list;
			$hero = $value->hero;
			$find = true;
			break;
		}
	}
	if(!$find)
	{
		$returnData -> fail = 6;
		break;
	}
	$myPlayer = createUserPlayer(1,1,$userData,$list,$hero,false);
	$myPlayer->hp = 1;
	// $myPlayer->force = 1000;
	
	$pkData = new stdClass();
	$pkData->seed = time();
	$pkData->endless = (30 + $index*5)*1000;
	$pkData->players = array();
	$pkData->check = true;
	array_push($pkData->players,$myPlayer);
	array_push($pkData->players,$enemy);
	
	$returnData -> pkdata = $pkData;
	$userData->addEnergy(-1);
	$userData->pk_common->pktype = 'endless';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->time = time();
	$userData->setChangeKey('pk_common');
	

	$info->enemy = $enemy;
	$info->num --;
	$sql = "update ".getSQLTable('endless')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);

}while(false);

?> 