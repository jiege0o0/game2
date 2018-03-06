<?php 
$id=$msg->id;
$type=$msg->type;


do{
	if($type == 'atk')
		$data = & $userData->atk_list->list;
	else
		$data = & $userData->def_list->list;
		
	$findData = &$data->{$id};
	
	if(!$findData)
	{
		$returnData -> fail = 1;
		break;
	}
	
	$findData->close = !$findData->close;
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