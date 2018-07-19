<?php 
$list=$msg->list;
require_once($filePath."pk/pk_tool.php");
$sql = "select * from ".getSQLTable('fight')." where gameid='".$userData->gameid."'";
$result = $conne->getRowsRst($sql);
$info = json_decode($result['info']);
do{		
	$award = explode(",",$info->award);
	foreach($list as $key=>$value)
	{
		$index = array_search($value, $award);
		$isOK = $index === 0 || $index>0;
		if(!$isOK)
		{
			$returnData -> fail = 1;
			break;
		}
		array_splice($award,$index,1);			
	}
	if($returnData -> fail)
		break;
	
	if($info->card)
	{
		// $chooseList = join(",",$list);
		$info->card = $info->card.','.join(",",$list);
	}
	else
		$info->card = join(",",$list);
	
	if($userData->hang->level > 30)	
		recordPKData('fight_get',join(",",$list));
		
	$info->award = '';
	$info->card = $info->card;


	$sql = "update ".getSQLTable('fight')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);

}while(false);



?> 