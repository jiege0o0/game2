<?php 
	require_once($filePath."cache/base.php");
	//当前等级下，产出单位个的间隔
	function getPropCD($clv,$slv,$tlv){
		$hourEarn = (floor(min($clv-$slv,100)/5) + 1)*(1 + $tlv*5/100);
		if($hourEarn <= 0)
			return 0;
		return 3600/$hourEarn;
	}


	do{
		$sql = "select * from ".getSQLTable('shop')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		if($result && isSameDate($result['time']))
		{
			$returnData->shop = json_decode($result['shop']);
			break;
		}

		//{id,num,diamond},
		//生成shop数据	
		$arr = array();
		$level = $userData->hang->level;
		/*foreach($prop_base as $key=>$value)
		{
			if($value['hanglevel'] && $value['hanglevel']<=$level)//资源道具
			{
				$propCD = getPropCD($level,$value['hanglevel'],$userData->getTecLevel(300 + $key));
				array_push($arr,array(
					'id'=>$key,
					'num'=>round(24*3600/$propCD),
					'times'=>0,
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
					'times'=>0,
					'diamond'=>$num * $value['diamond']
				));
			}
		}*/
		
		//资源宝箱
		for($i=1;$i<=24;$i++)
		{
			array_push($arr,array(
					'id'=>'box_resource',
					'num'=>$i,
					'times'=>0,
					'diamond'=>floor(pow($i,0.95) * 20)
				));
		}
	
				
		//钱
		/*$coinLevel = 0;
		for($i=1;$i<=22;$i++)
		{
			$coinLevel += $userData->getTecLevel(300 + $i);
		}
		$coin = floor(3600/10*0.3*pow($level,0.8)*(1+$coinLevel*0.002)*1.1 + $userData->hourcoin*1.1)*12;
		array_push($arr,array(
					'id'=>'coin',
					'num'=>$coin,
					'times'=>0,
					'diamond'=>60
				));*/
				
				
		//升级卡
		if($level >= 10 && $userData->getPropNum(101) == 0)
		{
			for($i=4;$i<=22;$i++)
			{
				$upProp[$i-2] = $prop_base[$i]['hanglevel'];
			}
			$passLevel = (int)$upProp[$userData->level];
			$nextLevel = (int)$upProp[$userData->level+1];
			if($nextLevel && $level < $nextLevel && (!$passLevel || $level>=$passLevel))
			{
				$need = $userData->level * 100 + 88;
				array_push($arr,array(
					'id'=>101,
					'num'=>1,
					'times'=>0,
					'level'=>$nextLevel,
					'diamond'=>$need
				));
			}
			
			
		}
		
		//必有1个技能
		$tecLevel = $userData->level;
		$skillArr = array();
		foreach($skill_base as $key=>$value)
		{
			if($value['level'] <= $tecLevel && $userData->getSkill($key) < 999)//@skill
			{
				$num = rand(5,10);
				array_push($skillArr,array('id'=>'skill'.$value['id'],'num'=>$num,'diamond'=>$num*10,'times'=>0,));
			}
		}
		
		

		$tempNum = 5;
		if($skillArr[0])
			$tempNum = 4;
		
		if(count($arr) > $tempNum)//最多取6个
		{
			usort($arr,randomSortFun);
			$arr = array_slice($arr,0,$tempNum);
		}
		//体力(必有)
		$num = rand(10,20);
		array_push($arr,array(
					'id'=>'energy',
					'num'=>$num,
					'times'=>0,
					'diamond'=>$num*5
				));
				
	
		
		if($skillArr[0])
		{
			usort($skillArr,randomSortFun);
			array_push($arr,$skillArr[0]);
		}
		
		
		
		
		$returnData->shop = $arr;
		if($result)
			$sql = "update ".getSQLTable('shop')." set shop='".json_encode($arr)."',time=".time()." where gameid='".$userData->gameid."'";
		else
			$sql = "insert into ".getSQLTable('shop')."(gameid,shop,time) values('".$userData->gameid."','".json_encode($arr)."',".time().")";
		$conne->uidRst($sql);

		
		
	}while(false);
	
?> 