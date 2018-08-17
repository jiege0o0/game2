<?php 
$id=$msg->id;
$type=$msg->type;
if($msg->list)
{
	$temp = str_replace("|",",",$msg->list);
	$list = explode(",",$temp);
}


do{
	if($type == 'atk')
		$data = &$userData->atk_list->list;
	else
		$data = &$userData->def_list->list;
			
	$findData = &$data->{$id};
	if(!$findData)
	{
		debug($data);
		$returnData -> fail = 1;
		break;
	}
	if($list || isset($msg->hero))
	{
		require_once($filePath."pos/test_list.php");
		if($returnData -> fail)
			break;
		if($msg->list)
			$findData->list = $msg->list;
		if(isset($msg->hero))
		{
			if($msg->hero)
				$findData->hero = $msg->hero;
			else
				$findData->hero = '';
		}
	}

	
		
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