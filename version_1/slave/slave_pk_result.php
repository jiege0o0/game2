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
	
	$playerData = getUserPKData($list,$pkData->players[0]);
	if($playerData -> fail)//出怪顺序有问题
	{
		$returnData -> fail = $playerData -> fail;
		break;
	}
	$pkData->players[0] = $playerData;
	$pkData->players[1] = getAutoPKData($pkData->players[1]);
	
	require_once($filePath."pk/pk.php");
	if($returnData -> fail)//PK结果有问题
	{
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
	$lastMaster = $result['master'];
	$time = time();
	$sql = "update ".getSQLTable('slave')." set master='".$userData->gameid."',addtime=".$time.",protime=".($time+3600*2).",awardtime=".$time." where gameid='".$otherid."'";
	$conne->uidRst($sql);
	
	//通知对方及其原主人
	$sqlArr = array();
	$sql = "update ".getSQLTable('user_open')." set masterstep=concat(masterstep,',1|".$time."'),slavetime=".$time.",mailtime=".$time." where gameid='".$otherid."'";
	$conne->uidRst($sql);
	
	$oo = new stdClass();
	$oo->nick = base64_encode($userData->nick);
	$oo->type = $userData->type;
	$oo = json_encode($oo);
	$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,time) values('".$userData->gameid."','".$otherid."',1,'".$oo."',".$time.")";
	$conne->uidRst($sql);
	
	if($master != $otherid)
	{
		$sql = "update ".getSQLTable('user_open')." set slavetime=".$time.",mailtime=".$time." where gameid='".$master."'";
		$conne->uidRst($sql);
		
		$oo = new stdClass();
		$oo->nick = base64_encode($userData->nick);
		$oo->slave_nick = base64_encode($userData->pk_common->nick);
		$oo->slave_gameid = $otherid;
		$oo->type = $userData->type;
		$oo = json_encode($oo);
		$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,time) values('".$userData->gameid."','".$otherid."',2,'".$oo."',".$time.")";
		$conne->uidRst($sql);
	}
	// $result = $conne->uidRst(join(";",$sqlArr));
	// debug($result);
	// debug(join(";",$sqlArr));
	

}while(false)


?> 