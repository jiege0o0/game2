<?php 

// $rankType = 'force';
// $rankScore = 10;
// require($filePath."rank/add_rank.php");


do{
	$sql = "select * from ".getSQLTable('slave')." where gameid in('".join(",",$msg->ids)."')";
	$result = $conne->getRowsArray($sql);
	$len = count($result);
	if(!$result || $len == 0)//没数据
	{
		$returnData -> fail = 1;
		break;
	}
	$time = time();
	$arr = array();
	$addCoin = 0;
	$changeTime = array();
	for($i=0;$i<$len;$i++)
	{
		$data = $result[$i];
		if($data['master']!=$userData->gameid)//主人变了
		{
			$returnData -> fail = 3;
			break;
		}
		
		$num = floor(($time - $data['awardtime'])/3600);
		if($num)
		{
			$addCoin += ceil(min(10,$num)*$data['hourcoin']*0.2);
			$changeTime[$data['gameid']] = $num;
			array_push($arr,"update ".getSQLTable('slave')." set awardtime=awardtime+".($num*3600)." where gameid='".$data['gameid']."'");
		}
		else//时间未到
		{
			$returnData -> fail = 2;
			break;
		}
	}
	
	if($returnData -> fail)
		break;
	
	$userData->addCoin($addCoin);
	for($i=0;$i<$len;$i++)
	{
		$conne->uidRst($arr[$i]);
		debug($arr[$i]);
	}
	$returnData->coin = $addCoin;
	$returnData->changetime = $changeTime;
	
}while(false)
?> 