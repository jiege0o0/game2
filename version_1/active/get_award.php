<?php 
do{
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
	if($award->skills)
	{
		foreach($award->skills as $key=>$value)
		{
			$userData->addSkill($key,$value);
		}
	}
	if($award->hero)
	{
		foreach($award->hero as $key=>$value)
		{
			$userData->addHero($key,$value);
		}
	}
}while(false)

?> 