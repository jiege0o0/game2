<?php 
	$otherid = $msg->otherid;
	$othernick = $msg->othernick;
	$returnData->stopLog = true;
	do{
		require_once($filePath."tool/conn.php");
		require_once($filePath."object/game_user.php");
		$sql = "select * from ".getSQLTable('user_data')." where gameid='".$otherid."' or nick='".$othernick."'";
		$result = $conne->getRowsRst($sql);
		if(!$result)
		{
			$returnData->fail = 1;
			break;
		}		
		
		$otherUser =  new GameUser($result);
		$returnUser = new stdClass();
		$returnUser->gameid = $otherUser->gameid;
		$returnUser->nick = $otherUser->nick;
		$returnUser->type = $otherUser->type;
		$returnUser->hourcoin = $otherUser->hourcoin;
		$returnUser->level = $otherUser->level;
		$returnUser->tec_force = $otherUser->tec_force;
		$returnUser->last_land = $otherUser->last_land;
		
		$returnData->info = $returnUser;
		
		
		$sql = "select * from ".getSQLTable('slave')." where gameid='".$otherUser->gameid."' or master='".$otherUser->gameid."'";
		$returnData->slave = $conne->getRowsArray($sql);

		
		
	}while(false);
	
?> 