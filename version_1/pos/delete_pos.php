<?php 
$id=$msg->id;
$type=$msg->type;


do{
	if($type == 'atk')
		$data = & $userData->atk_list->list;
	else
		$data = & $userData->def_list->list;
			
	foreach($data as $key=>$value)
	{
		if($value->id == $id)
		{
			$find = true;
			unset($data->{$key});
			break;
		}
	}
	
	if(!$find)
	{
		$returnData -> fail = 1;
		break;
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