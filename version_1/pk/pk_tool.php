<?php 
	//�����������
	function createUserPlayer($id,$team,$userData,$list){
		$player = new stdClass();
		$player->id = $id;
		$player->team = $team;
		$player->gameid = $userData->gameid;
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
	
	//������ҷ�������
	function createDefPlayer($id,$team,$userData,$list){
		$player = new stdClass();
		$player->id = $id;
		$player->team = $team;
		$player->gameid = $userData->gameid;
		$player->nick = base64_encode($userData->nick);
		$player->force = $userData->tec_force;
		$player->type = $userData->type;
		$player->autolist = $list;
		$player->hp = $userData->getHp();
		return $player;
	}
	
	//������������
	function createNpcPlayer($id,$team,$data){
		$player = new stdClass();
		$player->id = $id;
		$player->team = $team;
		$player->gameid = 'npc';
		$player->force = $data['force'];
		$player->type = $data['type'];
		$player->autolist = $data['list'];
		$player->hp = $data['hp'];
		return $player;
	}
	
	 //���Զ����н��н���
    function decodeAutoList($arr) {
        $returnArr = array();
        $mpCost = 0;
        $index = 1;
		$len = count($arr);
        for($i=0;$i<$len;$i++)
        {
            $group = explode("#",$arr[$i]);
            $mp = getGroupMp($group);//����MP
			
            $t = getMPTime($mpCost + $mp);//����ͬʱ�����ʱ���
			$len2 = count($group);
            for($j=0;$j<$len2;$j++)
            {
                $id = floor($group[$j]);
                if($id < 0)
                    continue;
				$oo = array(
				"mid"=>$id,
				"time"=>$t,
				"id"=>$index,
				);
                array_push($returnArr,$oo);
                $index ++;
            }
            $mpCost += $mp;
        }
        return $returnArr;
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
			else if($id < 100)
				$mp += $monster_base['cost'];
			else
				$mp += $skill_base['cost'];
        }
        return $mp;
    }

    //�����MP����ʱ��
    function getMPTime($mp){
        //30+40+60*3 = 250
        $step0 = 15;//��ʼֵ
        $step1 = 30;//��һ���Ӳ���
        $step2 = 40;//�ڶ����Ӳ���
        $step3 = 60;//֮��ÿ���ӵĲ���

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
	
	//�õ�����PK�����ݽṹ
	function getAutoPKData($player){
		$result = new stdClass();
		$result->list = decodeAutoList($player->autolist);
		$result->hp = $player->hp;
		$result->type = $player->type;
		$result->force = $player->force;
		$result->id = $player->id;
		$result->team = $player->team;
		
		
		return $result;
	}
	
	
	//�õ�����PK�����ݽṹ step#id,step#id,
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

		
		
		
		
		$stepCD = 50;
		$mpCost = 0;
		$list = explode(",",$list);
		$len = count($list);
		for($i=0;$i<$len;$i++)
        {
			$group = explode("#",$list[$i]);
			$time = $group[0]*$stepCD; 
			$id = $group[1];
			if($id < 100)
				$mpCost += $monster_base['cost'];
			else
				$mpCost += $skill_base['cost'];
			if(getMPTime($mpCost) > $time)//MP����
			{
				$result->fail = 101;
				break;
			}	
			$index = array_search($id, $orgin);
			$isOK = $index === 0 || ($index>0 && $index <6);//ֻ������ǰ6��
			if(!$isOK)//ʹ���˲��Ϸ��Ŀ�
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