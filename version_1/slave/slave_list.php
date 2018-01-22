<?php 
do{
	$sql = "select * from ".getSQLTable('slave')." where gameid='".$msg->gameid."' or master='".$msg->gameid."'";
	$result = $conne->getRowsArray($sql);
	if(!$result)
	{
		$sql = "insert into ".getSQLTable('slave')."(gameid,nick,type,hourcoin,tec_force,level,master) values('".$msg->gameid."','".$userData->nick."',".$userData->type.",".$userData->hourcoin.",".$userData->tec_force.",".$userData->level.",'".$msg->gameid."')";
		$conne->uidRst($sql);		
	}
	$returnData->list = $result;
}while(false)

?> 