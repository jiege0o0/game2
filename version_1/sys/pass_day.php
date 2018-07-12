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
	$returnData->mail_award = $addMailAward;
	

	
?> 