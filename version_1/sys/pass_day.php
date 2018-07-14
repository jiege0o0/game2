<?php

	$addMailAward = false;
	$time = time();
	if(!isSameDate($userData->last_land))
	{
	
		$oo = new stdClass();
		$oo->des = base64_encode('测试期间登录奖励');
		$oo->award = new stdClass();
		$oo->award->diamond = 100;
		$oo = json_encode($oo);
		$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,stat,time) values('sys','".$userData->gameid."',101,'".$oo."',0,".$time.")";
		$conne->uidRst($sql);
		
		$userData->openData['mailtime'] = $time;
		$userData->setOpenDataChange();
		$userData->setChangeKey('mailtime');
		
		$addMailAward = true;
		
		// debug($sql);
		// $writeDB = true;
	}
	
	if(!$userData->active->p0 || $userData->active->p0<1520498485)
	{
		$oo = new stdClass();
		$oo->title = base64_encode('新手礼包');
		$oo->des = base64_encode('欢迎加入到我们的大家庭，在此为你送上一点资源以表心意，祝你游戏愉快！');
		$oo->award = new stdClass();
		$oo->award->coin = 200;
		$oo->award->props = new stdClass();
		$oo->award->props->{1} = 20;
		$oo->award->props->{2} = 20;
		$oo->award->props->{3} = 20;
		$oo = json_encode($oo);
		$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,stat,time) values('sys','".$userData->gameid."',101,'".$oo."',0,".$time.")";
		$conne->uidRst($sql);
		
		
		$userData->active->p0 = $time;
		$userData->openData['mailtime'] = $time;
		$userData->setOpenDataChange();
		$userData->setChangeKey('mailtime');
		$userData->setChangeKey('active');
		
		$addMailAward = true;
		// $writeDB = true;
	}
	
	//改金币时产
	if(!$userData->active->p1)
	{
		$userData->active->p1 = $time;
		$userData->setChangeKey('active');	
		
		if($userData->hourcoin > 10)
		{
			$lastHourCoin = $userData->hourcoin;
			require_once($filePath."cache/base.php"); 
			$force = 10;
			foreach($tec_base as $key=>$value)
			{
				if($value['type'] == 3)
				{
					$level = $userData->getTecLevel($key);
					if($level)
					{
						$addValue = getTecValueX($level,$value['value1'],10);
						$force += $addValue;
					}
				}
			}
			$userData->hourcoin = $force;
			$returnData->sync_hourcoin = $force;
			$userData->setChangeKey('hourcoin');
			
			$rankType = 'hourcoin';
			$rankScore = $userData->hourcoin;
			require($filePath."rank/add_rank.php");
			require($filePath."slave/slave_reset_list.php");
			
			$oo = new stdClass();
			$oo->title = base64_encode('金币调整补偿');
			$oo->des = base64_encode('现在金币科技的收益提高啦，根据你当前的收益情况，现补偿你资源如下：');
			$oo->award = new stdClass();
			$oo->award->coin = 100*$lastHourCoin;
			$oo = json_encode($oo);
			$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,stat,time) values('sys','".$userData->gameid."',101,'".$oo."',0,".$time.")";
			$conne->uidRst($sql);
			
			
			$userData->openData['mailtime'] = $time;
			$userData->setOpenDataChange();
			$userData->setChangeKey('mailtime');
		
			$addMailAward = true;
		}
	}
	$returnData->mail_award = $addMailAward;
	
function getTecValueX($level,$begin,$step){
	$v = $begin;
	for($i=1;$i<$level;$i++)
	{	
		$v += max(1,floor($step*$i));
	}
	return $v;
}
	
?> 