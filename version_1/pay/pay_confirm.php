<?php 
	$time = time()-24*3600;
	do{
		$sql = "select count(*) as num from ".getSQLTable("pay_log")." where gameid='".$msg->gameid."' and orderno2='".$msg->order."' and time>".$time;
		$result = $conne->getRowsRst($sql);
		if($result['num'])//有对应订单
		{
			$returnData->ok = true;
			break;
		}
		$returnData->fail = 1;
			break;
		
		
	}while(false);
?> 