<?php 
$force1 = min(floor($userData->tec_force * 0.9),$userData->tec_force - 5);
$force2 = max(floor($userData->tec_force * 1.1),$userData->tec_force + 5);
do{
	$time = time();
	$sql = "select * from ".getSQLTable('slave')." where tec_force between ".$force1." and ".$force2." and gameid!='".$msg->gameid."' and master!='".$msg->gameid."' and protime<".$time." limit 30";
	$result = $conne->getRowsArray($sql);
	debug($sql);
	if(!$result || count($result) < 5)
	{
		if($result)
			$conne->close_rst();
		$force1 = min(floor($userData->tec_force * 0.8),$userData->tec_force - 5);
		$sql = "select * from ".getSQLTable('slave')." where tec_force between ".$force1." and ".$force2." and gameid!='".$msg->gameid."' and master!='".$msg->gameid."' and protime<".$time." limit 30";
		$result = $conne->getRowsArray($sql);
	}
	$returnData->list = $result;
}while(false)

?> 