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
		// $conne->uidRst($sql);
		array_push($runSqlArr,$sql);
		
		$userData->openData['mailtime'] = $time;
		$userData->setOpenDataChange();
		$userData->setChangeKey('mailtime');
		
		$addMailAward = true;
		$userData->setChangeKey('last_land');
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
		// $conne->uidRst($sql);
		array_push($runSqlArr,$sql);
		
		
		$userData->active->p0 = $time;
		$userData->openData['mailtime'] = $time;
		$userData->setOpenDataChange();
		$userData->setChangeKey('mailtime');
		$userData->setChangeKey('active');
		
		$addMailAward = true;
		// $writeDB = true;
	}
	
	/*if(!$userData->active->p7 && time() < 1534348800)
	{
		$oo = new stdClass();
		$oo->title = base64_encode('服务器异常补偿');
		$oo->des = base64_encode('在8月14日晚间由于服务器异常导致服务中断，现补偿以下内容');
		$oo->award = new stdClass();
		$oo->award->diamond = 60;
		$oo = json_encode($oo);
		$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,stat,time) values('sys','".$userData->gameid."',101,'".$oo."',0,".$time.")";
		// $conne->uidRst($sql);
		array_push($runSqlArr,$sql);
		
		
		$userData->active->p7 = 1;
		$userData->openData['mailtime'] = $time;
		$userData->setOpenDataChange();
		$userData->setChangeKey('mailtime');
		$userData->setChangeKey('active');
		
		$addMailAward = true;
		// $writeDB = true;
	}*/
	
	//改金币时产
	if(!$userData->active->p6)
	{
	
		$userData->active->p6 = 1;
		$userData->setChangeKey('active');	
		
		if($userData->hang->level >= 50)
		{
			require_once($filePath."cache/base.php"); 
			$oo = new stdClass();
			$oo->title = base64_encode('英雄礼包');
			$oo->des = base64_encode('英雄系统现已开启，现为你送上一份英雄礼包！');
			$oo->award = new stdClass();
			$oo->award->hero = new stdClass();
			$heroNum = ceil($userData->hang->level/30);
			$heroList = array();
			$tecLevel = $userData->level;
			foreach($monster_base as $key=>$value)
			{
				if($value['id'] > 100 && $value['id'] < 130 && $value['level']-1000 <= $tecLevel)
				{
					array_push($heroList,$key);
				}
			}
			$len = count($heroList)-1;
			while($heroNum>0)
			{
				$heroNum --;
				$heroid = $heroList[rand(0,$len)];
				if($oo->award->hero->{$heroid})
					$oo->award->hero->{$heroid}++;
				else
					$oo->award->hero->{$heroid} = 1;
			}
			
			$oo = json_encode($oo);
			$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,stat,time) values('sys','".$userData->gameid."',101,'".$oo."',0,".$time.")";
			// $conne->uidRst($sql);
			array_push($runSqlArr,$sql);
			
			$userData->openData['mailtime'] = $time;
			$userData->setOpenDataChange();
			$userData->setChangeKey('mailtime');
			$addMailAward = true;
		}
		
		
	}
	
	
	
	$returnData->mail_award = $addMailAward;
	
function getTecValueX($level,$step){
	$v = 1;
	for($i=1;$i<$level;$i++)
	{	
		$v += max(1,floor($step*$i));
	}
	return $v;
}
	
?> 