<?php 
$id=$msg->id;
$otherid=$msg->otherid;
$master=$msg->master;
require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");


do{	
	$maxNum = $userData->getMaxSlave();
	$sql = "select count(*) as num from ".getSQLTable('slave')." where master='".$msg->gameid."' and gameid!='".$msg->gameid."'";
	$result = $conne->getRowsRst($sql);
	if($result['num'] > $maxNum)//奴隶数达上限
	{
		$returnData -> fail = 8;
		break;
	}	
	
	$sql = "select * from ".getSQLTable('slave')." where gameid='".$otherid."'";
	$result = $conne->getRowsRst($sql);
	if(!$result)//找不到玩家
	{
		$returnData -> fail = 5;
		break;
	}
	
	if($result['master'] != $master)//敌人不一样
	{
		$returnData -> fail = 6;
		break;
	}
	
	if($result['protime'] > time())//保护中
	{
		$returnData -> fail = 7;
		break;
	}
	
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 1;
		break;
	}
	
	foreach($userData->atk_list->list as $key=>$value)
	{
		if($value->id == $id)
		{
			$list = $value->list;
			break;
		}
	}

	if(!$list)
	{
		$returnData -> fail = 2;
		break;
	}
	
	if(!deleteSkillCard($list))//技能卡数量不足
	{
		$returnData -> fail = 9;
		break;
	}
	
	$sql = "select * from ".getSQLTable('user_data')." where gameid='".$otherid."'";
	$otherData = $conne->getRowsRst($sql);
	if(!$otherData)//没有这个玩家
	{
		$returnData -> fail = 3;
		break;
	}
	$otherData = new GameUser($otherData);

	
	$defList = array();
	foreach($otherData->def_list->list as $key=>$value)
	{
		if(!$value->close)
		{
			array_push($defList,$value);
		}
	}
	
	$len = count($defList);
	if($len == 0)//没防御阵
	{
		$returnData -> fail = 4;
		break;
	}
	$defList = $defList[rand(0,$len-1)]->list;
	
	$sql = "select * from ".getSQLTable('slave')." where gameid='".$msg->gameid."'";
	$result = $conne->getRowsRst($sql);
	if(!$result)//找不到自己
	{
		$sql = "insert into ".getSQLTable('slave')."(gameid,nick,type,head,hourcoin,tec_force,level,master) values('".$userData->gameid."','".$userData->nick."',".$userData->type.",'".$userData->head."',".$userData->hourcoin.",".$userData->tec_force.",".$userData->level.",'".$userData->gameid."')";
		$conne->uidRst($sql);	
	}

	
	$pkData = new stdClass();
	$pkData->seed = time();
	$pkData->players = array();
	array_push($pkData->players,createUserPlayer(1,1,$userData,$list));
	array_push($pkData->players,createDefPlayer(2,2,$otherData,$defList));
	
	$returnData -> pkdata = $pkData;
	$userData->addEnergy(-1);
	$userData->pk_common->pktype = 'slave';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->pkcard = $list;
	$userData->pk_common->otherid = $otherid;
	$userData->pk_common->master = $master;
	$userData->pk_common->nick = base64_encode($otherData->nick);
	$userData->pk_common->time = time();
	$userData->setChangeKey('pk_common');

}while(false)


?> 