<?php 
$list=$msg->list;

require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");

$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$info = json_decode($result['info']);
do{		
	if($userData->pk_common->pktype != 'fight')//最近不是打这个
	{
		$returnData -> fail = 1;
		break;
	}
	
	if($userData->pk_common->lastkey == $msg->key)
	{
		$lastData = $userData->pk_common->lastreturn;
		foreach($lastData as $key=>$value)
		{
			$returnData ->{$key} = $value;
		}
		break;
	}
	$userData->pk_common->lastkey = $msg->key;
	$userData->pk_common->lastreturn = $returnData;
	$userData->setChangeKey('pk_common');
	
	if($userData->pk_common->level != $info->step)
	{
		$returnData -> fail = 2;
		break;
	}
	
	
	$userData->pk_common->lastkey == $msg->key;
	$userData->pk_common->lastreturn = $returnData;
	
	
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
		if($value->mid < 500)
		{
			$index = array_search($value->mid, $card);
			array_splice($card,$index,1);			
		}
	}
	$info->card = join(",",$card);
	$info->step ++;
	$info->enemy = '';
	
	
	//补充卡组
	$tecLevel = min($userData->tec_force/10,950);
	$skillArr = array();
	$awardSkillArr = array();
	foreach($skill_base as $key=>$value)
	{
		if($value['level'] <= $tecLevel)
		{
			array_push($skillArr,$value['id']);
			array_push($skillArr,$value['id']);
			if($userData->getSkill($value['id']) < 999)
				array_push($awardSkillArr,$value['id']);
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
	$addCoin = ($info->step)*15 + floor($force/20);
	$award->coin = $addCoin;
	$userData->addCoin($addCoin);
	
	$addValue = $info->step*8;
	$award->fightvalue = $addValue;
	$info->value += $addValue;
	
	//奖励1-2个技能
	if(count($awardSkillArr)>0)
	{
		$award->skills = new stdClass();
		usort($awardSkillArr,randomSortFun);
		$num = rand(1,2);
		for($i=0;$i<$num;$i++)
		{
			if($awardSkillArr[$i])
			{
				$award->skills->{$awardSkillArr[$i]} = 1;
				$userData->addSkill($awardSkillArr[$i],1);
			}
		}
	}
	
	
	$returnData->award = $award;
	$returnData->cardaward = $info->award;
	$returnData->card = $info->card;
	

	$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);

}while(false);



?> 