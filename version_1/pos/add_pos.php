<?php 
$id=$msg->id;
$type=$msg->type;
$name=$msg->name;
$temp = str_replace("|",",",$msg->list);
$list = explode(",",$temp);
if(isset($msg->hero))
	$hero = explode(",",$msg->hero);

do{
	if($type == 'atk')
		$data = $userData->atk_list->list;
	else
		$data = $userData->def_list->list;
		
	if(count($data) >= 5)
	{
		$returnData -> fail = 1;
		break;
	}
	require_once($filePath."pos/test_list.php");
	if($returnData -> fail)
		break;
		
		
	$posData = new stdClass();
	$posData->id = $id;
	$posData->list = $msg->list;
	if($msg->hero)
		$posData->hero = $msg->hero;
	if($name)
		$posData->name = base64_encode($name);

		
	if($type == 'atk')
	{
		$userData->atk_list->list->{$id} = $posData;
		$userData->setChangeKey('atk_list');
	}
	else
	{
		$userData->def_list->list->{$id} = $posData;
		$userData->setChangeKey('def_list');
	}
	
}while(false)


?> 