<?php 
	require_once($filePath."cache/base.php");
	$tecLevel = $userData->level;
	$skillArr = array();
	if(rand(0,1) == 0)
	{
		foreach($monster_base as $key=>$value)
		{
			if($value['level'] <= $tecLevel)
			{
				array_push($skillArr,$key);
			}
		}
	}
	else
	{
		foreach($skill_base as $key=>$value)
		{
			if($value['level'] <= $tecLevel)
			{
				array_push($skillArr,$key);
			}
		}
	}
	shuffle($skillArr);
	$skillArr = array_slice($skillArr,0,3);
?> 