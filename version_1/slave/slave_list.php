<?php 
do{
	$sql = "select * from ".getSQLTable('slave')." where gameid='".$msg->gameid."' or master='".$msg->gameid."'";
	$result = $conne->getRowsArray($sql);
	debug($userData->head);
	debug($sql);
	if(!$result)
	{
		$sql = "insert into ".getSQLTable('slave')."(gameid,nick,type,head,hourcoin,tec_force,level,master,protime,logintime) values('".$msg->gameid."','".$userData->nick."',".$userData->type.",'".$userData->head."',".$userData->hourcoin.",".$userData->tec_force.",".$userData->level.",'".$msg->gameid."',0,".time().")";
		$conne->uidRst($sql);	
		debug($sql);
		$returnData->slave = array();	
		$userData->active->slave_open=true;	
		$userData->setChangeKey('active');		
		// $returnData->master;		
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