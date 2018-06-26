<?php 
$list=$msg->list;
$hangIndex=$userData->hang->level + 1;
require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");
do{	
	
	if($userData->pk_common->pktype != 'slave')//最近不是打这个
	{
		$returnData -> fail = 1;
		break;
	}
	$pkData = $userData->pk_common->pkdata;
	
	$playerData = getUserPKData($list,$pkData->players[0],$msg->cd,$msg->key);
	if($playerData -> fail)//出怪顺序有问题
	{
		$returnData -> fail = $playerData -> fail;
		break;
	}
	
	$otherid = $userData->pk_common->otherid;
	$master = $userData->pk_common->master;
	$sql = "select * from ".getSQLTable('slave')." where gameid='".$otherid."'";
	$result = $conne->getRowsRst($sql);
	if($result['master'] != $master)
	{
		$returnData -> fail = 2;
		$returnData -> otherid = $otherid; 
		break;
	}
	
	if($result['protime'] > time())//保护中
	{
		$returnData -> fail = 3;
		break;
	}
	
	$pkMaster = $userData->gameid == $otherid;
	$lastMaster = $result['master'];
	$time = time();
	$protime = $pkMaster ?($time+300):($time+3600*2);
	$sql = "update ".getSQLTable('slave')." set master='".$userData->gameid."',addtime=".$time.",protime=".$protime.",awardtime=".$time." where gameid='".$otherid."'";
	$conne->uidRst($sql);
	
	//通知对方及其原主人
	$sql = "update ".getSQLTable('user_open')." set masterstep=concat(masterstep,',".($pkMaster?0:1)."|".$time."'),slavetime=".$time.",mailtime=".$time." where gameid='".$otherid."'";
	$conne->uidRst($sql);
	
	
	$videoPKData = new stdClass();
	$videoPKData->pkdata = $pkData;
	$videoPKData->pklist = $list;
	$videoPKData->version = $pk_version;
	
	if(!$pkMaster)//成为主人
	{
		$oo = new stdClass();
		$oo->nick = base64_encode($userData->nick);
		$oo->type = $userData->type;
		$oo->head = $userData->head;
		$oo->rd = rand(0,9);
		$oo->pkdata = $videoPKData;
		$oo = json_encode($oo);
		$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,time) values('".$userData->gameid."','".$otherid."',1,'".$oo."',".$time.")";
		$conne->uidRst($sql);
	}
	else//反抗成功
	{
		$oo = new stdClass();
		$oo->nick = base64_encode($userData->nick);
		$oo->type = $userData->type;
		$oo->head = $userData->head;
		$oo->rd = rand(0,9);
		$oo->pkdata = $videoPKData;
		$oo = json_encode($oo);
		$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,time) values('".$userData->gameid."','".$master."',4,'".$oo."',".$time.")";
		$conne->uidRst($sql);
	}
	
	
	if($master != $otherid)//抢奴隶,发给原主人
	{
		$sql = "update ".getSQLTable('user_open')." set slavetime=".$time.",mailtime=".$time." where gameid='".$master."'";
		$conne->uidRst($sql);
		
		$oo = new stdClass();
		$oo->nick = base64_encode($userData->nick);
		$oo->slave_nick = base64_encode($userData->pk_common->nick);
		$oo->slave_gameid = $otherid;
		$oo->type = $userData->type;
		$oo->head = $userData->head;
		$oo->rd = rand(0,9);
		$oo->pkdata = $videoPKData;
		$oo = json_encode($oo);
		$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,time) values('".$userData->gameid."','".$otherid."',2,'".$oo."',".$time.")";
		$conne->uidRst($sql);
	}

	

}while(false)


?> 