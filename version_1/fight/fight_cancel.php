<?php 
$list=$msg->list;


$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$info = json_decode($result['info']);
do{		
	
	
	
	$info->card = '';
	$info->award = '';
	$info->step = 0;

	$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);

}while(false);



?> 