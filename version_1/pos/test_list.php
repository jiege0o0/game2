<?php 
	require_once($filePath."cache/base.php");
	$monster = array();
	foreach($list as $key=>$value)
	{
		if(!$monster[$value])
		{
			$skillID = (int)$value;
			debug($userData);
			if($skillID < 100 && $monster[$skillID]['level'] > 1 &&!in_array($skillID,$userData->card->monster,true))
			{
				$returnData -> fail = 2;
				break;
			}
			if($skillID > 100 && $skill[$skillID]['level'] > 1 && !in_array($skillID,$userData->card->skill,true))
			{
				$returnData -> fail = 2;
				break;
			}
			$monster[$value] = 1;
		}
		else
		{
			$monster[$value] ++;
			if($monster[$value] > 3)
			{
				$returnData -> fail = 3;
				break;
			}	
		}
	}
	
?> 