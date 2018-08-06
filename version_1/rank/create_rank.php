<?php 
	//
	$min =  (int)date('i');
	$fileName = 'rank'.date('YmdH').floor($min/4).'_'.$serverID;
	$file1  = $dataFilePath.'rank/'.$fileName.'.txt';//今天的排行榜数据
	do{
		if(is_file($file1))//文件已生成,这个罗辑已被其它人触发了
		{
			$returnData->fail = 1;					
			break;
		}
		
		require_once($filePath."tool/conn.php");
		file_put_contents($file1,'',LOCK_EX);
		
		$sql = "select * from ".getSQLTable('pvp_offline')." ORDER BY score DESC limit 100";
		debug($sql);
		$result = $conne->getRowsArray($sql);
		$len = count($result);
		$arr1= array();
		if($result)
		{
			foreach($result as $key=>$value)
			{
				$oo = json_decode($value['data']);
				array_push($arr1,array(
					'gameid'=>$value['gameid'],
					'score'=>$value['score'],
					'time'=>$value['time'],
					'nick'=>base64_decode($oo->nick),
					'head'=>$oo->head,
					'type'=>$oo->type
				));
			}
		}
		file_put_contents($file1,json_encode($arr1),LOCK_EX);		
		$returnData->ok = true;
	}while(false);
	
	
?> 