<?php 
$list=$msg->list;
$hangIndex=$userData->hang->level + 1;
require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");
do{		
	if($userData->pk_common->pktype != 'hang' || $userData->pk_common->level != $hangIndex)//������Ǵ����
	{
		$returnData -> fail = 1;
		break;
	}
	$pkData = $userData->pk_common->pkdata;
	
	$playerData = getUserPKData($list,$pkData->players[0]);
	if($playerData -> fail)//����˳��������
	{
		$returnData -> fail = $playerData -> fail;
		break;
	}
	$pkData->players[0] = $playerData;
	$pkData->players[1] = getAutoPKData($pkData->players[1]);
	
	require_once($filePath."pk/pk.php");
	if($returnData -> fail)//PK���������
	{
		break;
	}
	

	$userData->hang->level = $hangIndex;
	if(!$userData->hang->awardtime)
		$userData->hang->awardtime = time();
	$userData->hang->pktime = time();
	$userData->setChangeKey('hang');
	$returnData->level = $userData->hang->level;
	$returnData->pktime = $userData->hang->pktime;
	

}while(false)


?> 