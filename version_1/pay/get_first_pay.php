<?php 

	do{
	if(!$userData->rmb)
	{
		$returnData -> fail = 1;
		break;
	}
	if($userData->active->first_pay)
	{
		$returnData -> fail = 2;
		break;
	}
	$userData->active->first_pay = 1;
	$userData->setChangeKey('active');
	
	
	$award = new stdClass();
	$award->hero = array();
	$list = array(101,106,108,113,116);
	foreach($list as $key=>$value)
	{
		$userData->addHero($value,1);	
		$award->hero[$value] = 1;	
	}
	$returnData -> award = $award;

	
	
	}while(false);
?> 