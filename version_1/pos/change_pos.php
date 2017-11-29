<?php 
$id=$msg->id;
$name=$msg->name;
$close=$msg->close;
$type=$msg->type;
$list = $msg->list;

do{
	if($type == 'atk')
		$data = &($userData->atk_list->list);
	else
		$data = &($userData->def_list->list);
			
	foreach($data as $key=>$value)
	{
		if($value->id == $id)
		{
			$findData = &$data[$key];
			break;
		}
	}
	if(!$findData)
	{
		$returnData -> fail = 1;
		break;
	}
	if($list)
	{
		require_once($filePath."pos/test_list.php");
		if($returnData -> fail)
			break;
		$findData->list = $list;
	}
	$findData->name = $name;
	$findData->close = $close;
	
		
	if($type == 'atk')
	{
		$userData->setChangeKey('atk_list');
	}
	else
	{
		$userData->setChangeKey('def_list');
	}
}while(false)


?> 