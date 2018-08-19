<?php 
	//创建玩家数据
	function createUserPlayer($id,$team,$userData,$list,$hero,$isAuto=false){
		$player = new stdClass();
		$player->id = $id;
		$player->team = $team;
		$player->gameid = $userData->gameid;
		$player->head = $userData->head;
		$player->nick = base64_encode($userData->nick);
		$player->force = $userData->tec_force;
		$player->type = $userData->type;
		if($isAuto)
		{
			$player->autolist = $list;
		}
		else
		{
			$list = explode(",",$list);
			$len = count($list);
			if($len <= 6)
			{
				$player->card = join(",",$list);
			}
			else
			{	
				$len = ceil(($len-6)/6);
				$newList = array_slice($list,0,6);
				for($i=0;$i<$len;$i++)
				{
					$temp = array_slice($list,6 + $i*6,6);
					usort($temp,randomSortFun);
					$newList = array_merge($newList,$temp);
				}
				$player->card = join(",",$newList);
			}
		}
		
		if($hero)
		{
			$arr = explode(",",$hero);
			for($i=0;$i<5;$i++)
			{
				$hid = $arr[$i];
				if(!$hid)
					$hid = 0;
				$heroLevel = $userData->getHeroLevel($hid);
				if($userData->maxHeroLevel)
					$heroLevel = min($heroLevel,$userData->maxHeroLevel);
				$arr[$i] = $hid.'|'.$heroLevel;
			}
			$player->hero = join(",",$arr);
		}
		
		$player->hp = $userData->getHp();
		return $player;
	}
	
	//创建玩家防御数据
	function createDefPlayer($id,$team,$userData,$list,$hero){
		$player = new stdClass();
		$player->id = $id;
		$player->team = $team;
		$player->gameid = $userData->gameid;
		$player->head = $userData->head;
		$player->nick = base64_encode($userData->nick);
		$player->force = $userData->tec_force;
		$player->type = $userData->type;
		$player->def = 10;
		$player->autolist = $list;
		$player->hp = $userData->getHp();
		if($hero)
		{
			$arr = explode(",",$hero);
			for($i=0;$i<5;$i++)
			{
				$hid = $arr[$i];
				if(!$hid)
					$hid = 0;
				$arr[$i] = $hid.'|'.$userData->getHeroLevel($hid);
			}
			$player->hero = join(",",$arr);
		}
		return $player;
	}
	
	//创建怪物数据
	function createNpcPlayer($id,$team,$data){
		$player = new stdClass();
		$player->id = $id;
		$player->team = $team;
		$player->gameid = 'npc';
		$player->force = $data['force'];
		$player->type = $data['type'];
		$player->autolist = $data['list'];
		$player->hp = $data['hp'];
		$player->hero = $data['hero'];
		$player->def = 5;
		
		//找头像
		$list = explode(",",$player->autolist);
		$index = 0;
		while($list[$index] && $list[$index] > 200)//@skillID
			$index ++;
		if($list[$index])
			$player->head = $list[$index];
		else
			$player->head = 1;
		return $player;
	}
	
	function recordPKData($type,$str,$add=''){
		global $dataFilePath,$userData;
		$file  = $dataFilePath.'log/use_'.$type.'.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
		file_put_contents($file, $userData->level.'|'.$str.'|'.$add.PHP_EOL,FILE_APPEND);
	}

    function getGroupMp($group){
		global $monster_base,$skill_base;
        $mp = 0;
		$len = count($group);
        for($j=0;$j<$len;$j++)
        {
            $id = floor($group[$j]);
            if($id < 0)
            {
                $mp += -$id;
                continue;
            }
			else if($id < 200)//@skillID
				$mp += $monster_base['cost'];
			else
				$mp += $skill_base['cost'];
        }
        return $mp;
    }

    //到这个MP量的时间
    function getMPTime($mp){
        //30+40+60*3 = 250
        $step0 = 15;//初始值
        $step1 = 30;//第一分钟产量
        $step2 = 40;//第二分钟产量
        $step3 = 60;//之后每分钟的产量

        if($mp <= $step0)
            return 0;
        $mp -= $step0;

        if($mp <= $step1)
            return $mp/$step1 * 60*1000;

        $mp -= $step1;
        if($mp <= $step2 )
            return $mp/$step2 * 60*1000 + 60*1000;

        $mp -= $step2;
        return $mp/$step3 * 60*1000 + 60*1000*2;

    }
	
	function getMPList(){
		$mpList = array(0);
		$max = 250;
		for($i=1;$i<=$max;$i++)
		{
			$mpList[$i] = getMPTime($i);
		}
		return $mpList; 
	}
	
	function addMPTime(&$arr,$time,$mp){
		$len = count($arr);
		for($i=0;$i<$len;$i++)
		{
			if($arr[$i] >= $time)
			{
				while($mp>0)
				{
					array_splice($arr,$i,0,$time);
					$mp--;
				}
				break;
			}
		}
    }	
	
	function deleteSkillCard($card){
		global $userData,$returnData;
		$arr = explode(",",$card);
		$len = count($arr);
		$orginSkillNum = new stdClass();
		for($i=0;$i<$len;$i++)
		{
			$skillID = (int)$arr[$i];
			if($skillID >= 200)//@skillID
			{
				$num = $userData->getSkill($skillID);
				if(!$orginSkillNum->{$skillID})
					$orginSkillNum->{$skillID} = $num;
				if($num>0)
				{
					if($num < 999)//@skillID  
						$userData->addSkill($skillID,-1);
				}
				else
				{
					$returnData->sync_skill->{$skillID} = $orginSkillNum->{$skillID};
					return false;
				}
			}
		}
		return true;
    }
	
	//返还技能卡牌
	function backSkillCard($list){
		global $userData;
		foreach($list as $key=>$skillID)
		{
			$num = $userData->getSkill($skillID);
			if($num < 999)
				$userData->addSkill($skillID,1);
		}
	}
	
	
	//得到用于PK的数据结构 step#id,step#id,
	function getUserPKData($list,$player,$cd,$key,$seed){
		global $monster_base,$skill_base;
		
		
		$result = new stdClass();
		$result->list = array();
		$result->hp = $player->hp;
		$result->type = $player->type;
		$result->force = $player->force;
		$result->id = $player->id;
		$result->team = $player->team;
		$result->isauto = !$player->card;
		$result->skill = array();
		
		
		$card = $player->card;
		if(!$card)
			$card = $player->autolist;
		$orgin = explode(",",$card);
			
        $serverKey = substr(md5($cd.$card.$list.$seed),-8);
		if($serverKey != $key)//校验不通过
		{
			$result->fail = 103;
			debug($seed);
			debug($serverKey);
			return $result;
		}	

		
		
		
		$mpList = getMPList();
		$stepCD = 50;
		$mpCost = 0;
		if($list)
		{
			$list = explode(",",$list);
			$len = count($list);
			for($i=0;$i<$len;$i++)
			{
				$group = explode("#",$list[$i]);
				$time = $group[0]*$stepCD; 
				$id = $group[1];
				if($id < 200)//@skillID
				{
					$mpCost += $monster_base[$id]['cost'];
				}
				else
				{
					$mpCost += $skill_base[$id]['cost'];
					if($skill_base[$id]['sv4'] == -10001)
					{
						addMPTime($mpList,$time + 3000 + $skill_base[$id]['cd']*1000,$skill_base[$id]['sv1']+ $skill_base[$id]['cost']);
					}
				}

				if($mpList[$mpCost] > $time)//MP不够
				{
					$result->fail = 101;
					break;
				}	
				if($id < 500)//非报警卡
				{
					$index = array_search($id, $orgin);
					$isOK = $index === 0 || ($index>0 && $index <6);//只可以用前6张
					if(!$isOK)//使用了不合法的卡
					{
						$result->fail = 102;
						break;
					}
					array_splice($orgin,$index,1);
				}
				
				array_push($result->list,array(
					"mid"=>$id,
					"time"=>$time,
					"id"=>$i,
					));
			}
		}
		
		
		//记录未使用的技能卡
		$len = count($orgin);
		for($i=0;$i<$len;$i++)
		{
			$id = $orgin[$i];
			if($id > 200)//@skillID
				array_push($result->skill,$id);
		}
		return $result;
	}
	
	
?> 