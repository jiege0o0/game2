<?php 
	$min =  (int)date('i');
	$fileName = 'rank'.date('YmdH').floor($min/4).'_'.$serverID;
	$file  = $dataFilePath.'rank/'.$fileName.'.txt';//今天的排行榜数据
	$returnData->stopLog = true;
	
	do{
		if(!is_file($file))//文件未生成
		{
			$returnData->fail = 1;
			break;
		}
		$content = file_get_contents($file);
		if(!content)//文件已生成，但内容为空
		{
			$returnData->fail = 2;
			break;
		}
		$returnData->list = $content;
	}while(false);
	
?> 