<?php 
	$id = $msg->id;
	$arr = array(
		'101'=>array('cost'=>6,'diamond'=>100),
		'102'=>array('cost'=>30,'diamond'=>520),
		'103'=>array('cost'=>150,'diamond'=>2650),
		'104'=>array('cost'=>600,'diamond'=>10650),
		'105'=>array('cost'=>1,'diamond'=>1)
	);
	
	
	do{
		$userData->addDiamond($arr[$id]['diamond'],true);
		$userData->rmb += $arr[$id]['cost'];
		$userData->setChangeKey('rmb');
		
		payLog(json_encode($msg));
		
		if($msg->order)
		{	
			$sql = "insert into ".getSQLTable('pay_log')."(gameid,orderno,orderno2,time,goodsid) values('".$userData->gameid."','".$msg->order."','".$msg->localOrder."',".time().",'".$id."')";
			$conne->uidRst($sql);
		}
		
		
	}while(false);
?> 