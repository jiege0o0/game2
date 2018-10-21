<?php 
$list=$msg->list;
$hangIndex=$userData->hang->level + 1;
require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");
do{		
	if($userData->pk_common->pktype != 'hang')//最近不是打这个
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
	
	
	if($userData->pk_common->level != $hangIndex)//最近不是打这个
	{
		$returnData -> fail = 2;
		break;
	}
	$pkData = $userData->pk_common->pkdata;
	
	$playerData = getUserPKData($list,$pkData->players[0],$msg->cd,$msg->key,$pkData->seed);
	backSkillCard($playerData->skill);
	$enempList = $pkData->players[1]->autolist;
	if($playerData -> fail)//出怪顺序有问题
	{
		$returnData -> fail = $playerData -> fail;
		break;
	}
	
	// $upProp = array(-1);//19个
	// for($i=4;$i<=22;$i++)
	// {
		// array_push($upProp,$prop_base[$i]['hanglevel']);
	// }
	$award = new stdClass();	
	if($hangIndex >= 50 && $hangIndex%5 == 0)//英雄
	{
		$awardNum = 1;
		require_once($filePath."pay/box_hero.php");
	}
	
	$award->props = array();
	$addCoin = 800 + $hangIndex*10;//90+$hangIndex*15 + floor($hangIndex/5)*30;
	// $index = array_search($hangIndex, $upProp);
	// debug($index);
	// debug($userData->getPropNum(101) + ($userData->level - 1));
	// if($index && $userData->getPropNum(101) + ($userData->level - 1) < $index)
	// {
		// $award->props[101] = 1;
		// $userData->addProp(101,1);
		// $addCoin += 500;
	// }
	
	$userData->addCoin($addCoin);
	$award->coin = $addCoin;
	
	if($hangIndex > 0 && $hangIndex%8 == 0)
	{
		$award->props[101] = 1;
		$userData->addProp(101,1);
	}
	
	if($hangIndex <= 20)//道具
	{
		$num = 3 + floor($hangIndex/5);
		$award->props[1] = $num;
		$userData->addProp(1,$num);
		
		$award->props[2] = $num;
		$userData->addProp(2,$num);
		
		$award->props[3] = $num;
		$userData->addProp(3,$num);
		if($hangIndex >= 8)
		{
			$award->props[4] = $num;
			$userData->addProp(4,$num);
		}
	}
	
	if($hangIndex > 20)//技能
	{
		$tecLevel = $userData->level;
		$skillArr = array();

		foreach($skill_base as $key=>$value)
		{
			if($value['level'] <= $tecLevel && $userData->getSkill($key) < 999)//@skill
			{
				array_push($skillArr,$key);
			}
		}

		if($skillArr[0])
		{
			$award->skills = array();
			shuffle($skillArr);
			$skillID = $skillArr[0];
			$award->skills[$skillID] = 1;
			$userData->addSkill($skillID,1);
		}
	}
	
	// if(!$award->props[101] && $hangIndex%2 == 0)
	// {
		// $award->props[102] = 1;
		// $userData->addProp(102,1);
	// }
	
	
	
	// $propArr = array();
	// if($hangIndex > 10)
		// $propLevel = $hangIndex + 5;
	// else
		// $propLevel = $hangIndex;
	
	// foreach($prop_base as $key=>$value)
	// {
		// if($value['hanglevel'] && $value['hanglevel']<=$propLevel)
		// {
			// array_push($propArr,$value);
		// }
	// }
	// usort($propArr,"my_hang_sort");
	// $addProp = $propArr[rand(0,2)];
	// $num = (int)min(100,max(0,($hangIndex - $addProp['hanglevel'])/10) + 5);
	// $award->props[$addProp['id']] = $num;
	// $userData->addProp($addProp['id'],$num);
	
	$returnData->award = $award;
	

	$userData->hang->level = $hangIndex;
	if(!$userData->hang->awardtime)
		$userData->hang->awardtime = time();
	$userData->hang->pktime = time();
	$userData->hang->lastlist = $enempList;
	$userData->setChangeKey('hang');
	$returnData->level = $userData->hang->level;
	$returnData->pktime = $userData->hang->pktime;
	$returnData->lastlist = $userData->hang->lastlist;
	
	//入榜
	$rankType = 'hang';
	$rankScore = $hangIndex;
	require($filePath."rank/add_rank.php");
	
	debug($userData->hang->level);
	if($userData->hang->level > 10)
	{
		//入录像
		$info = new stdClass();
		$info->gameid = $userData->gameid;
		$info->nick = base64_encode($userData->nick);
		$info->type = $userData->type;
		$info->head = $userData->head;
		$info->force = $playerData->force;
		$info->isauto = $playerData->isauto;
		$info->cd = $msg->cd;
		$info->version = $pk_version;
		

		$data = new stdClass();
		$data->pkdata = $pkData;
		$data->pklist = $list;
		

		
		$sql = "update ".getSQLTable('video')." set info='".json_encode($info)."',data='".json_encode($data)."',time=".time()." where level=".($userData->hang->level)." order by time limit 1";
		$conne->uidRst($sql);
		debug($sql);
	}
	
	
	

}while(false);

function my_hang_sort($a,$b)
{
	if ($a['hanglevel'] > $b['hanglevel'])
		return -1;
	if ($a['hanglevel'] < $b['hanglevel'])
		return 1;
	return 0;
}


?> 