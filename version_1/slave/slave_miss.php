<?php 
$force1 = min(floor($userData->tec_force * 0.9),$userData->tec_force - 10);
$force2 = max(floor($userData->tec_force * 1.1),$userData->tec_force + 10);
do{
	$time = time();
	$sql = "select * from ".getSQLTable('slave')." where tec_force between ".$force1." and ".$force2." and gameid!='".$msg->gameid."' and master!='".$msg->gameid."' and protime<".$time." ORDER BY logintime DESC limit 20";
	$result = $conne->getRowsArray($sql);
	//debug($sql);
	if(!$result || count($result) < 4)
	{
		if($result)
			$conne->close_rst();
		$force1 = min(floor($userData->tec_force * 0.8),$userData->tec_force - 10);
		$sql = "select * from ".getSQLTable('slave')." where tec_force between ".$force1." and ".$force2." and gameid!='".$msg->gameid."' and master!='".$msg->gameid."' and protime<".$time." ORDER BY logintime DESC limit 20";
		$result = $conne->getRowsArray($sql);
	}
	$returnData->list = $result;
}while(false)

?> 