<?php 
	require_once($filePath."tool/tool.php");
	require_once($filePath."tool/conn.php");
	require_once($filePath."object/game_user.php");
	
	
	do{
		$sql = "select * from ".getSQLTable("user_data")." where gameid='".$msg->gameid."'";
		$userData = $conne->getRowsRst($sql);
		// echo $sql.'<br/>';
		
		if(!$userData)//登录失效
		{
			$returnData->fail = 1;
			break;
		}
		if($msg->order)
		{
			$sql = "select count(*) as num from ".getSQLTable("pay_log")." where gameid='".$msg->gameid."' and orderno='".$msg->order."'";
			$result = $conne->getRowsRst($sql);
			if($result['num'])//有重复订单
			{
				$returnData->fail = 2;
				break;
			}
		}
		
		$userData = new GameUser($userData,true);
		require_once($filePath."pay/add_diamond.php");
		$userData->write2DB();
	}while(false);
?> 