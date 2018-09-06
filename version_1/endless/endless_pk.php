<?php 
$answerPath=$msg->path;
$index=$msg->index;
require_once($filePath."pk/pk_tool.php");
require_once($dataFilePath."active/".$answerPath.".php");
do{		
	if(!$userData->testEnergy(1))//没体力
	{
		$returnData -> fail = 1;
		break;
	}
	// $returnData->question=$question;
	
	$sql = "select * from ".getSQLTable('answer')." where gameid='".$userData->gameid."'";
	$result = $conne->getRowsRst($sql);
	if(!$result)
	{
		$returnData -> fail = 2;
		break;
	}
	
	$info = json_decode($result['info']);
	if($info->num <=0)
	{
		$returnData -> fail = 3;
		break;
	}
	
	if($info->index != $index-1)
	{
		$returnData -> fail = 4;
		break;
	}

	
	$answerData = $question[$index];

		
	$enemyData=array('force'=>1000,'type'=>0,'list'=>$answerData['question'],'hp'=>3);	
	$enemy = createNpcPlayer(2,2,$enemyData);
	$enemy->def = 0;
	$enemy->nick = base64_encode('迷题'.$index);
	
	$myPlayerData=array('force'=>1000,'type'=>0,'list'=>$answerData['answer'],'hp'=>3);	
	$myPlayer = createNpcPlayer(1,1,$myPlayerData);
	$myPlayer->def = 0;
	$myPlayer->gameid = $userData->gameid;
	$myPlayer->card = $myPlayer->autolist;
	unset($myPlayer->autolist);
	$myPlayer->nick = base64_encode($userData->nick);
	
	$pkData = new stdClass();
	$pkData->seed = time();
	$pkData->players = array();
	$pkData->check = true;
	array_push($pkData->players,$myPlayer);
	array_push($pkData->players,$enemy);
	
	$returnData -> pkdata = $pkData;
	$userData->addEnergy(-1);
	$userData->pk_common->pktype = 'answer';
	$userData->pk_common->pkdata = $pkData;
	$userData->pk_common->time = time();
	$userData->setChangeKey('pk_common');
	

	$info->num --;
	$sql = "update ".getSQLTable('answer')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);

}while(false);

?> 