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
	$hero = $info->hero;
	
	//产生敌人
	if(!$info->enemy)
	{
		require_once($filePath."cache/base.php");
		//计算关卡战力
		$force= $userData->tec_force + max(round($info->step/25*$userData->tec_force),2*$info->step);
		$enemy = array();
		$enemy['force'] = $force;
		
		$tecLevel = $userData->level;
		$skillArr = array();
		$heroArr = array();
		foreach($monster_base as $key=>$value)
		{
			if($value['level'] <= $tecLevel)
			{
				array_push($skillArr,$value);
			}
			else if($value['id'] > 100 && $value['id'] < 130 && $value['level']-1000 <= $tecLevel)
			{
				array_push($heroArr,$value);
			}
		}
		shuffle($skillArr);
		$skillArr = array_slice($skillArr,0,4);
		usort($skillArr,"my_fight_sort");
		array_splice($skillArr,rand(2,3),1);
		$arr = array();
		$len = max(2,round($userData->maxCardNum()*0.2)) + $info->step;//*ceil($userData->maxCardNum()*0.05);//$userData->maxCardNum() + 3;
		for($i=0;$i<$len;$i++)
		{
			array_push($arr,$skillArr[rand(0,2)]['id']);
		}
		$enemy['list'] = join(",",$arr);
		

		
		
		$enemy['type'] = $skillArr[0]['type'];
		$enemy['hp'] = $userData->getHp();
		
		if($info->step > 3 && $userData->hang->level >= 50)//加入英雄
		{
			$heroLevel = max(1,min(5,floor(pow($userData->hang->level/100,0.8))));
			shuffle($heroArr);
			$skillArr = array_slice($heroArr,0,min(5,$info->step - 5));
			foreach($skillArr as $key=>$value)
			{
				$skillArr[$key] = $value['id'].'|'.$heroLevel;
			}
			$enemy['hero'] = join(",",$skillArr);
		}
		
		
		$player = createNpcPlayer(2,2,$enemy);
		$nick = '远征守卫'.($info->step + 1);
		$player->nick = base64_encode($nick);
		
		$info->enemy = $player;
		
		$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);
	}
	
	
	$pkData = new stdClass();
	$pkData->check = true;
	$pkData->seed = time();
	$pkData->players = array();
	array_push($pkData->players,createUserPlayer(1,1,$userData,$list,$hero));
	array_push($pkData->players,$info->enemy);

	
	
	$returnData -> pkdata = $pkData;
	$userData->addEnergy(-1);
	$userData->pk_common->pktype = 'fight';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->level = $info->step;
	$userData->pk_common->time = time();
	$userData->setChangeKey('pk_common');
	
	

}while(false);

function my_fight_sort($a,$b)
{
	if ($a['cost'] < $b['cost'])
		return -1;
	if ($a['cost'] > $b['cost'])
		return 1;
	return 0;
}

?> 