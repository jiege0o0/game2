<?php 
	header('Access-Control-Allow-Origin:*');
	error_reporting(E_ALL^(E_NOTICE|8192));
	// error_reporting(0);
	ini_set('display_errors', '1');
	
	
	
	$sign = $_GET["sign"];//MD5(orderno+username + serverid+addgold + rmb+paytime+ext+key)
	$orderno = $_GET["orderno"];
	$username = $_GET["username"];
	$serverid = $_GET["serverid"];
	$addgold = $_GET["addgold"];
	$rmb = $_GET["rmb"];
	$paytime = $_GET["paytime"];
	$ext = $_GET["ext"];
	$key = '_pay';
	
	$md5Key = md5($orderno.$username.$serverid.$addgold.$rmb.$paytime.$ext.$key);
	if($sign == $md5Key)
	{
		$temp = explode("|",$ext);
		$returnData = new stdClass();
		
		$msg = new stdClass();
		$msg->gameid = $temp[0];
		$msg->serverid = $temp[1];
		$msg->id = $temp[2];
		$msg->localOrder = $temp[3];
		$msg->order = $orderno;
		$msg->h5='qunhei';
		$serverID = $msg->serverid;
		
		// http://172.17.196.195:90/pay_qunhei.php?orderno=123&ext=1_10024|1|101
		// echo $filePath.'<br/>';
		// echo $filePath."platform_pay.php";
		require_once(dirname(__FILE__)."/game_version_path.php");
		require_once($filePath."pay/platform_pay.php");
		if($returnData->fail == 1)//账号不存在
			echo '-2';
		else if($returnData->fail == 2)//订单号重复
			echo '-5';
		// else if($returnData->fail == 666)//验证码错误
			// echo '-4';
		else//充值成功  1
			echo '1';
	}
	else
		echo '-4';
	
	// echo '<br/>'.$returnData->fail;
	
	
	
?> 