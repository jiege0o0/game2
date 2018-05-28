<?php 
$list=$msg->list;

require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");

$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$info = json_decode($result['info']);
do{		
	if($userData->pk_common->pktype != 'fight' || $userData->pk_common->level != $info->step)//最近不是打这个
	{
		$returnData -> fail = 1;
		break;
	}
	$pkData = $userData->pk_common->pkdata;
	
	$playerData = getUserPKData($list,$pkData->players[0],$msg->cd,$msg->key);
	$enempList = $pkData->players[1]->autolist;
	if($playerData -> fail)//出怪顺序有问题
	{
		$returnData -> fail = $playerData -> fail;
		break;
	}
	
	$force = $info->enemy->force;
	
	//减去手牌
	$card = explode(",",$info->card);
	foreach($playerData->list as $key=>$value)
	{
		$index = array_search($value->mid, $card);
		array_splice($card,$index,1);			
	}
	$info->card = join(",",$card);
	$info->step ++;
	$info->enemy = '';
	
	
	$tecLevel = min($userData->tec_force/10,950);
	$skillArr = array();
	foreach($skill_base as $key=>$value)
	{
		if($value['level'] <= $tecLevel)
		{
			array_push($skillArr,$value['id']);
			array_push($skillArr,$value['id']);
		}
	}
	$tecLevel = $userData->getTecLevel(1);
	foreach($monster_base as $key=>$value)
	{
		if($value['level'] <= $tecLevel)
		{
			array_push($skillArr,$value['id']);
			array_push($skillArr,$value['id']);
			array_push($skillArr,$value['id']);
		}
	}
	
	usort($skillArr,randomSortFun);
	$skillArr = array_slice($skillArr,0,9);
	
	$info->award = join(",",$skillArr);
	
	
	
	
	$award = new stdClass();
	$addCoin = ($info->step)*10 + floor($force/20);
	$award->coin = $addCoin;
	$userData->addCoin($addCoin);
	
	$addValue = $info->step*10;
	$award->fightvalue = $addValue;
	$info->value += $addValue;
	
	$returnData->award = $award;
	$returnData->cardaward = $info->award;
	$returnData->card = $info->card;
	

	$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);

}while(false);



?> 