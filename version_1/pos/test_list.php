<?php 
	require_once($filePath."cache/base.php");
	$useData = array();
	$maxNum =  $userData->maxCardNum();
	if(count($list) > $maxNum)
	{
		$returnData -> fail = 4;
		$returnData -> maxNum = $maxNum; 
	}
	else
	{
		foreach($list as $key=>$value)
		{
			if(!$useData[$value])
			{
				$skillID = (int)$value;
				if($skillID < 200 && $monster_base[$skillID]['level'] > 1 &&!in_array($skillID,$userData->card->monster,true))//@skillID
				{
					$returnData -> fail = 2;
					$returnData -> id = $skillID;
					break;
				}
				if($skillID > 200 && !$userData->card->skill->{$skillID})//@skillID
				{
					$returnData -> fail = 2;
					$returnData -> id = $skillID;
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
					break;
				}	
			}
		}
	}
	
	
?> 