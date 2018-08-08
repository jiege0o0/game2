<?php
	require_once($filePath."tool/conn.php");
	require_once($filePath."object/game_user.php");
	$gameid = $serverID.'_'.$msg->id;
	$sql = "select * from ".getSQLTable('user_data')." where gameid='".$gameid."'";
	$userData = $conne->getRowsRst($sql);
	
	if($userData)//有这个玩家
	{
		$time = time();
		// $sql = "update ".getSQLTable('user_data')." set last_land=".$time.",land_key='".$time."' where gameid='".$gameid."'";
		// $conne->uidRst($sql);

		$sql = "update ".getSQLTable('slave')." set logintime=".$time." where gameid='".$gameid."'";
		$conne->uidRst($sql);
		
		//开放数据
		$sql = "select * from ".getSQLTable('user_open')." where gameid='".$gameid."'";
		$userOpen = $conne->getRowsRst($sql);
		if(!$userOpen)
		{
			$sql2 = "insert into ".getSQLTable('user_open')."(gameid) values('".$gameid."')";
			$conne->uidRst($sql2);
			$userOpen = $conne->getRowsRst($sql);
		}
		$writeDB = true;
		$lastLand = $userData['last_land'];
		// $userData['last_land'] = $time;
		$userData['land_key'] = $time;
		$userData = new GameUser($userData,$userOpen);
		$userData->setChangeKey('land_key');
		// $userData->addCoin(1);
		if($userData->resetCoin())
		{
			//$writeDB = true;
			unset($returnData->sync_coin);
		}
		
		//用户数据处理
		unset($userData->hang->cd);
		unset($userData->active->p1);
		unset($userData->active->p2);
		unset($userData->active->p3);
		unset($userData->active->p4);
		require_once($filePath."sys/pass_day.php");
		// $addMailAward = false;
		
		// if(!isSameDate($lastLand))
		// {
			// $oo = new stdClass();
			// $oo->des = base64_encode('测试期间登录奖励');
			// $oo->award = new stdClass();
			// $oo->award->diamond = 100;
			// $oo = json_encode($oo);
			// $sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,stat,time) values('sys','".$userData->gameid."',101,'".$oo."',0,".$time.")";
			// $conne->uidRst($sql);
			
			// $userData->openData['mailtime'] = $time;
			// $userData->setOpenDataChange();
			// $userData->setChangeKey('mailtime');
			
			// $addMailAward = true;
		// }
		
		// if(!$userData->active->p0 || $userData->active->p0<1520498485)
		// {
			// $oo = new stdClass();
			// $oo->title = base64_encode('新手礼包');
			// $oo->des = base64_encode('欢迎加入到我们的大家庭，在此为你送上一点资源以表心意，祝你游戏愉快！');
			// $oo->award = new stdClass();
			// $oo->award->coin = 200;
			// $oo->award->props = new stdClass();
			// $oo->award->props->{1} = 20;
			// $oo->award->props->{2} = 20;
			// $oo->award->props->{3} = 20;
			// $oo = json_encode($oo);
			// $sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,stat,time) values('sys','".$userData->gameid."',101,'".$oo."',0,".$time.")";
			// $conne->uidRst($sql);
			
			
			// $userData->active->p0 = $time;
			// $userData->openData['mailtime'] = $time;
			// $userData->setOpenDataChange();
			// $userData->setChangeKey('mailtime');
			// $userData->setChangeKey('active');
			
			// $addMailAward = true;
		// }
		
		if($writeDB)
		{
			$userData->write2DB(true);
		}
		
		//未开邮件
		if($msg->mailtime)
		{
	
			if($addMailAward)
				$returnData->mailnum = 1;
			else
			{
				$msgtime = max($msg->mailtime,time() - 72*3600);
				$sql = "select * from ".getSQLTable('mail')." where to_gameid='".$userData->gameid."' and type>100 and stat!=1 and time>".$msgtime;
				$result = $conne->getRowsArray($sql);
				debug($sql);
				if($result)
					$returnData->mailnum = count($result);
			}
		}
		
		
		
		//其它数据返回
		$userData->pk_version = $pk_version;
		$returnData->data = $userData;
		$userData->opentime = $serverOpenTime;
		
		$logtime = 1533690591;
		if($msg->logtime < $logtime)
		{
			$returnData->logtext = new stdClass();
			$returnData->logtext->text = 
				'新增竞技场功能（天梯），玩家可在里面进行无战力差别战斗，并完成竞技任务获得奖励|'.
'新增市场功能，玩家可在其中进行资源平衡|'.
'加入阵容自主命名功能|'.
'重复挑战时，可在布阵时看到敌方阵容和历史对战记录|'.
'商城及远征商店调整，去除分散资源，加入资源宝箱，技能宝箱等|'.
'调整背包入口至科技界面|'.
'观看录像时，只有真人对战才可切换视角|'.
'游戏体验优化，BUG修复';
			$returnData->logtext->time = $logtime;
		}
		
		
	}
	else//没这个玩家，要新增
	{
		$returnData-> fail = 2;
		$returnData-> stopLog = true;
	}
?> 