<?php 
	do{
		if($round <= 1)
			break;
		$file  = $dataFilePath.'log/pvp_end_'.$serverID.'_'.($round - 1).'.txt';
		if(is_file($file))//文件已生成,这个罗辑已被其它人触发了
		{				
			break;
		}
		file_put_contents($file,''.time(),LOCK_EX);
		$sql = "update ".getSQLTable('pvp_offline')." set score=(score-3000)/2+3000  where score>3000";
		$conne->uidRst($sql);	
	}while(false);		
?> 