<?php 
	require_once($filePath."cache/base.php");
	//当前等级下，产出单位个的间隔
	function getPropCD($clv,$slv,$tlv){
		$hourEarn = ($clv-$slv + 1)*(1 + $tlv*5/100);
		if($hourEarn <= 0)
			return 0;
		return 3600/$hourEarn;
	}


	do{
		$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if($result && isSameDate($result['time']))
		{
			$returnData->shop = json_decode($result['shop']);
			$returnData->info = json_decode($result['info']);
			break;
		}
		//-----------------跨天了----------------
		
		if(!$result)
		{
			$info = new stdClass();
			$info->num = 1;//免费次数
			$info->step = -1;//当前步骤
			$info->card = '';//上阵的卡
			$info->enemy = '';//当前敌人
			$info->award = '';//待选列表
			$info->value = 0;//积分
		}
		else 
		{
			$info = json_decode($result['info']);
			$info->num = 1;
		}

		//{id,num,diamond},
		//生成shop数据	
		$arr = array();
		$level = $userData->hang->level;
		if(!$level)
			$level = 1;
		foreach($prop_base as $key=>$value)
		{
			if($value['hanglevel'] && $value['hanglevel']<=$level)//资源道具
			{
				$propCD = getPropCD($level,$value['hanglevel'],$userData->getTecLevel(300 + $key));
				array_push($arr,array(
					'id'=>$key,
					'num'=>round(24*3600/$propCD),
					'diamond'=>60
				));
			}
			else if($value['diamond'] && $key != 101)
			{
				//技能令...
				$num = rand(1,5);
				array_push($arr,array(
					'id'=>$key,
					'num'=>$num,
					'diamond'=>$num * $value['diamond']
				));
			}
		}
	
				
		//钱
		$coinCD = 3600/(90+$level*10 + floor($level/5)*20);
		array_push($arr,array(
					'id'=>'coin',
					'num'=>round(24*3600/$coinCD),
					'diamond'=>60
				));
		
		//体力
		$num = rand(10,20);
		array_push($arr,array(
					'id'=>'energy',
					'num'=>$num,
					'diamond'=>$num*5
				));

		
		if(count($arr) > 3)//最多取3个
		{
			usort($arr,randomSortFun);
			$arr = array_slice($arr,0,3);
		}
		
		//必有3个技能
		$tecLevel = min($userData->tec_force/10,950);
		$skillArr = array();
		foreach($skill_base as $key=>$value)
		{
			if($value['level'] <= $tecLevel && $userData->getSkill($key) < 999)//@skill
			{
				$num = rand(5,10);
				array_push($skillArr,array('id'=>'skill'.$value['id'],'num'=>$num,'diamond'=>$num*10));
			}
		}
		usort($skillArr,randomSortFun);
		$skillArr = array_slice($skillArr,0,3);
		
		$arr = array_merge($arr,$skillArr);

		$returnData->shop = $arr;
		$returnData->info = $info;
		if($result)
			$sql = "update ".getSQLTable('fight')." set shop='".json_encode($arr)."',info='".json_encode($info)."',time=".time()." where gameid='".$userData->gameid."'";
		else
			$sql = "insert into ".getSQLTable('fight')."(gameid,shop,info,time) values('".$userData->gameid."','".json_encode($arr)."','".json_encode($info)."',".time().")";
		$conne->uidRst($sql);

		
		
	}while(false);
	
?> 