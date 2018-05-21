<?php 
	require_once($filePath."cache/base.php");
	//��ǰ�ȼ��£�������λ���ļ��
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
		//-----------------������----------------
		
		if(!$result)
		{
			$info = new stdClass();
			$info->num = 1;//��Ѵ���
			$info->level = 0;//��ʼ�ȼ�
			$info->maxlevel = 0;//��ߵȼ�
			$info->step = 0;//��ǰ����
			$info->card = '';//����Ŀ�
			$info->enemy = '';//��ǰ����
			$info->award = '';//��ѡ�б�
			$info->value = 0;//����
		}
		else 
		{
			$info = json_decode($result['info']);
			$info->num = 1;
		}

		//{id,num,diamond},
		//����shop����	
		$arr = array();
		$level = $userData->hang->level;
		if(!$level)
			$level = 1;
		foreach($prop_base as $key=>$value)
		{
			if($value['hanglevel'] && $value['hanglevel']<=$level)//��Դ����
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
				//������...
				$num = rand(1,5);
				array_push($arr,array(
					'id'=>$key,
					'num'=>$num,
					'diamond'=>$num * $value['diamond']
				));
			}
		}
	
				
		//Ǯ
		$coinCD = 3600/(90+$level*10 + floor($level/5)*20);
		array_push($arr,array(
					'id'=>'coin',
					'num'=>round(24*3600/$coinCD),
					'diamond'=>60
				));
		//������
		if($level >= 10 && $userData->getPropNum(101) == 0)
		{
			array_push($arr,array(
					'id'=>101,
					'num'=>1,
					'diamond'=>$prop_base[101]['diamond']
				));
		}
		
		//����
		$num = rand(10,20);
		array_push($arr,array(
					'id'=>'energy',
					'num'=>$num,
					'diamond'=>$num*5
				));

		
		if(count($arr) > 3)//���ȡ4��
		{
			usort($arr,randomSortFun);
			$arr = array_slice($arr,0,3);
		}
		
		//����3������
		$tecLevel = $info->maxlevel;
		$skillArr = array();
		foreach($skill_base as $key=>$value)
		{
			if($value['level'] <= $tecLevel)
			{
				$num = rand(5,10);
				array_push($skillArr,array('id'=>'skill'.$value['id'],'num'=>$num,'diamond'=>$num*8));
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