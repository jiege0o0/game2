<?php 
	$id = $msg->id;
	do{
		$sql = "select * from ".getSQLTable('choose')." where gameid='".$userData->gameid."'";
		$result = $conne->getRowsRst($sql);
		$info = json_decode($result['info']);
		
		if(!in_array($id, $info->choose))
		{
			$returnData->fail = 1;
			break;
		}
		$cardlist = explode(",",$info->cardlist);
		array_push($cardlist,$id);
		if(count($cardlist)<30)
		{
			require($filePath."choosecard/random_choosecard.php");
			$info->choose = $skillArr;
		}	
		else
			$info->choose ='';
			
		$info->cardlist = join(",",$cardlist);
		
		$sql = "update ".getSQLTable('answer')." set info='".json_encode($info)."',time=".time()." where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);
		
	}while(false);
	
?> 