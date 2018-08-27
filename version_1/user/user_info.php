<?php 
	$otherid = $msg->otherid;
	$othernick = $msg->othernick;
	$otheruid = $msg->otheruid;
	$returnData->stopLog = true;
	do{
		require_once($filePath."tool/conn.php");
		require_once($filePath."object/game_user.php");
		require_once($filePath."cache/base.php");
		if($otherid)
			$sql = "select * from ".getSQLTable('user_data')." where gameid='".$otherid."'";
		else if($othernick)
			$sql = "select * from ".getSQLTable('user_data')." where nick='".$othernick."'";
		else 
			$sql = "select * from ".getSQLTable('user_data')." where uid=".$otheruid."";
		debug($sql);
		$result = $conne->getRowsRst($sql);
		if(!$result)
		{
			$returnData->fail = 1;
			break;
		}		
		
		$otherUser =  new GameUser($result,null,1);
		$returnUser = new stdClass();
		$returnUser->gameid = $otherUser->gameid;
		$returnUser->uid = $otherUser->uid;
		$returnUser->nick = $otherUser->nick;
		$returnUser->head = $otherUser->head;
		$returnUser->type = $otherUser->type;
		$returnUser->hourcoin = $otherUser->hourcoin;
		$returnUser->level = $otherUser->level;
		$returnUser->tec_force = $otherUser->tec_force;
		$returnUser->last_land = $otherUser->last_land;
		$returnUser->last_card = $otherUser->pk_common->pkcard;
		$returnUser->last_hero = $otherUser->pk_common->pkhero;
		$returnUser->hero_level = $otherUser->card->hero; 
		debug($otherUser->card);
		
		$returnUser->hp = $otherUser->getHp();
		$returnUser->maxslave = $otherUser->getMaxSlave();
		$returnUser->maxcard = $otherUser->maxCardNum();
		
		
		
		$returnData->info = $returnUser;
		
		
		$conne->close_rst();
		$sql = "select * from ".getSQLTable('slave')." where gameid='".$otherUser->gameid."' or master='".$otherUser->gameid."'";
		$result = $conne->getRowsArray($sql);
		if($result)
		{
			foreach($result as $key=>$value)
			{
				if($value['gameid'] == $otherUser->gameid)
				{
					$returnData->self = $value;	
					array_splice($result,$key,1);
					if($value['gameid'] != $value['master'])
					{
						$conne->close_rst();
						$sql = "select * from ".getSQLTable('slave')." where gameid='".$value['master']."'";
						$master = $conne->getRowsRst($sql);
					}
				}
			}
		}
		
		$returnData->slave = $result;		
		$returnData->master = $master;	

		
		
	}while(false);
	
?> 