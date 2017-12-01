<?php 
$name=$msg->name;
$type=$msg->type;
$temp = str_replace("|",",",$msg->list);
$list = explode(",",$temp);

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
		
	$haveID = array();	
	foreach($data as $key=>$value)
	{
		array_push($haveID,$value->id);
	}
	$id = rand(1,999);
	while(in_array($id,$haveID,true))
		$id++;
		
	$posData = new stdClass();
	$posData->id = $id;
	$posData->name = $name;
	$posData->list = $msg->list;
	
	$returnData->id = $id;
		
	if($type == 'atk')
	{
		array_push($userData->atk_list->list,$posData);
		$userData->setChangeKey('atk_list');
	}
	else
	{
		array_push($userData->def_list->list,$posData);
		$userData->setChangeKey('def_list');
	}
}while(false)


?> 