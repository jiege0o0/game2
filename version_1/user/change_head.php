<?php 
	$headID = $msg->headid;
	// $cost = 60;
	do{
		// if($userData->diamond < $cost)
		// {
			// $returnData->fail = 4;
			// $returnData->sync_diamond = $userData->diamond;
			// break;
		// }
		$userData->head = $headID;
		$userData->setChangeKey('head');	

		// $userData->addDiamond(-$cost);	

		require($filePath."slave/slave_reset_list.php");		
	}while(false);
	
?> 