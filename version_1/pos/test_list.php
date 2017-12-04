<?php 
	require_once($filePath."cache/base.php");
	$useData = array();
	foreach($list as $key=>$value)
	{
		if(!$useData[$value])
		{
			$skillID = (int)$value;
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
			$useData[$value] = 1;
		}
		else
		{
			$useData[$value] ++;
			if($useData[$value] > 3)
			{
				$returnData -> fail = 3;
				debug($userData);
				break;
			}	
		}
	}
	
?> 