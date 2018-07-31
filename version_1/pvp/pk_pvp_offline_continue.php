<?php 
$id=$msg->id;
require_once($filePath."pk/pk_tool.php");

do{		
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 1;
		break;
	}
	
	$pkData = $userData->pk_common->pkdata;
	$enemy = $pkData->players[1];
	
	
	$pkData = new stdClass();
	$pkData->seed = time();
	$pkData->players = array();
	$pkData->check = true;
	
	foreach($userData->def_list->list as $key=>$value)
	{
		if($value->id == $id)
		{
			$list = $value->list;
			break;
		}
	}
	$myPlayer = createUserPlayer(1,1,$userData,$list,true);
	$myPlayer->force = 500;
	array_push($pkData->players,$myPlayer);
	array_push($pkData->players,$enemy);

	
	$returnData -> pkdata = $pkData;
	$userData->addEnergy(-1);
	$userData->pk_common->pktype = 'pvp_offline';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->time = time();
	$userData->setChangeKey('pk_common');
	
	

}while(false);

?> 