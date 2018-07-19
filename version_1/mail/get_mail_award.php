<?php 
do{
	$sql = "select * from ".getSQLTable('mail')." where id=".$msg->id."";
	$result = $conne->getRowsRst($sql);
	debug($result);
	if(!$result || $result['type'] < 100)//Ã»Êý¾Ý
	{
		$returnData -> fail = 1;
		break;
	}
	if($result['stat'])//done!
	{
		$returnData -> fail = 3;
		break;
	}
	
	$oo = json_decode($result['content']);
	$award = $oo->award;
	if($award->coin)
	{
		$userData->addCoin($award->coin);
	}
	if($award->diamond)
	{
		$userData->addDiamond($award->diamond);
	}
	if($award->energy)
	{
		$userData->addEnergy($award->energy);
	}
	if($award->props)
	{
		foreach($award->props as $key=>$value)
		{
			$userData->addProp($key,$value);
		}
	}
	
	
	$sql = "update ".getSQLTable('mail')." set stat=1 where id=".$msg->id."";
	if(!$conne->uidRst($sql))
	{
		$returnData -> fail = 2;
		break;
	}
	
	
	$returnData->award = $award;

	
}while(false)
?> 