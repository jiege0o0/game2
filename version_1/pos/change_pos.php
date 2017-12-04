<?php 
$id=$msg->id;
$name=$msg->name;
$type=$msg->type;
if($msg->list)
{
	$temp = str_replace("|",",",$msg->list);
	$list = explode(",",$temp);
}


do{
	if($type == 'atk')
		$data = & $userData->atk_list->list;
	else
		$data = & $userData->def_list->list;
			
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
		$findData->list = $msg->list;
	}
	if($name)
		$findData->name = base64_encode($name);
	
		
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