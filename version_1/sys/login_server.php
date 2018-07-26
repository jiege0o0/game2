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
		
		$logtime = 1532310675;
		if($msg->logtime < $logtime)
		{
			$returnData->logtext = new stdClass();
			$returnData->logtext->text = 
				'[【重要】]降低属性相克的影响，防御加成：[50%->20%],攻击加成：[100%->50%]|'.
'降低科技升级的金币消耗，但增加宝石消耗,同时科技的增加值也有调整，曲线更加合理|'.
'资源科技加入金币效率提升，现在升级资源科技，能同时提升战役中获取金币和宝石的效率了|'.
'奴隶在战役中可获得自身与主人战力差的[5%]的战力加成|'.
'技能获取等级调整，现在能获得更多种类的技能了|'.
'更换头像现在免费了|'.
'增加自动挑战模式，现在玩家过关可以自动操作了|'.
'游戏体验优化，BUG修复|'.
'以下是卡牌属性调整：|'.
'强化大部分的法术卡牌|'.
'宝石狂徒：基础攻击[10->12]（因为造成3次伤害，实际每次攻击30->36）|'.
'狂战士：基础攻击[40->50]，溅射伤害[50%->35%]|'.
'幻影剌客：闪避机率[50%->60%]|'.
'碧玉药菇：加上技能生效上限，[同一时间]最多只有[3个]药菇的回血技能生效|'.
'橙光仙菇：调整伤害算法，打出上限伤害的阀值调低|'.
'骷髅士兵:基础生命[20->10]，出生士兵数量[2->3]个|'.
'骷髅矿工:基础攻击[40->50],基础生命[100->10],护盾抵挡次数[3->5]|'.
'希望大家能给这个游戏打分投票，让更多玩家参与进来，后面新增的实时PK功能需要足够数量的玩家才能匹配起来';
			$returnData->logtext->time = $logtime;
		}
		
	}
	else//没这个玩家，要新增
	{
		$returnData-> fail = 2;
		$returnData-> stopLog = true;
	}
?> 