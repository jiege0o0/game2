<?php 
	//创建玩家数据
	function createUserPlayer($id,$team,$userData,$list){
		$player = new stdClass();
		$player->id = $id;
		$player->team = $team;
		$player->gameid = $userData->gameid;
		$player->head = $userData->head;
		$player->nick = base64_encode($userData->nick);
		$player->force = $userData->tec_force;
		$player->type = $userData->type;
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
		$player->hp = $userData->getHp();
		return $player;
	}
	
	//创建玩家防御数据
	function createDefPlayer($id,$team,$userData,$list){
		$player = new stdClass();
		$player->id = $id;
		$player->team = $team;
		$player->gameid = $userData->gameid;
		$player->head = $userData->head;
		$player->nick = base64_encode($userData->nick);
		$player->force = $userData->tec_force;
		$player->type = $userData->type;
		$player->autolist = $list;
		$player->hp = $userData->getHp();
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
	
	function addMPTime($arr,$time,$mp){
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
	
	
	//得到用于PK的数据结构 step#id,step#id,
	function getUserPKData($list,$player){
		global $monster_base,$skill_base;
		$orgin = explode(",",$player->card);
		
		$result = new stdClass();
		$result->list = array();
		$result->hp = $player->hp;
		$result->type = $player->type;
		$result->force = $player->force;
		$result->id = $player->id;
		$result->team = $player->team;

		
		
		
		$mpList = getMPList();
		$stepCD = 50;
		$mpCost = 0;
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
					addMPTime(&$mpList,$time + 3000 + $skill_base[$id]['cd']*1000,$skill_base[$id]['sv1']);
				}
			}
				
				// if(id > 0)
            // {
                // var vo = CM.getCardVO(id);
                // var mp = vo.cost
                // var t = mpList[mpCost + mp]//可以同时上阵的时间点
                // returnArr.push({
                    // mid:id,
                    // time:t,
                    // id:index
                // })
                // index ++;

                // if(!vo.isMonster && vo.sv4 == -10001)
                // {
                    // this.addMPTime(mpList,t + PKConfig.beforeCD + vo.cd,vo.sv1)
                // }
            // }
            // else
            // {
                // var mp = -id;
            // }
			if($mpList[$mpCost] > $time)//MP不够
			{
				$result->fail = 101;
				break;
			}	
			$index = array_search($id, $orgin);
			$isOK = $index === 0 || ($index>0 && $index <6);//只可以用前6张
			if(!$isOK)//使用了不合法的卡
			{
				$result->fail = 102;
				break;
			}
			array_splice($orgin,$index,1);
			array_push($result->list,array(
				"mid"=>$id,
				"time"=>$time,
				"id"=>$i,
				));
		}
		return $result;
	}
?> 