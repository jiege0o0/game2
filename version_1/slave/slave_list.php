<?php 
do{
	$sql = "select * from ".getSQLTable('slave')." where gameid='".$msg->gameid."' or master='".$msg->gameid."'";
	$result = $conne->getRowsArray($sql);
	if(!$result)
	{
		$sql = "insert into ".getSQLTable('slave')."(gameid,nick,type,hourcoin,tec_force,level,master) values('".$msg->gameid."','".$userData->nick."',".$userData->type.",".$userData->hourcoin.",".$userData->tec_force.",".$userData->level.",'".$msg->gameid."')";
		$conne->uidRst($sql);	

		$returnData->slave = array();		
		$returnData->master = array();		
	}
	else
	{
		
		foreach($result as $key=>$value)
		{
			if($value['gameid'] == $msg->gameid)
			{
				$returnData->self = $value;	
				array_splice($result,$key,1);
				if($value['gameid'] != $value['master'])
				{
					$conne->close_rst();
					$sql = "select * from ".getSQLTable('slave')." where gameid='".$value['master']."'";
					$master = $conne->getRowsRst($sql);
				}
			}
		}
		$returnData->slave = $result;		
		$returnData->master = $master;	
	}
	
}while(false)

?> 