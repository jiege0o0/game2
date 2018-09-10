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
		if($info->cardlist)
			$cardlist = explode(",",$info->cardlist);
		else
			$cardlist = array();
		array_push($cardlist,$id);
		if(count($cardlist)<30)
		{
			require($filePath."choosecard/random_choosecard.php");
			$info->choose = $skillArr;
		}	
		else
			$info->choose ='';
			
		$info->cardlist = join(",",$cardlist);
		$returnData->choose = $info->choose;
		
		$sql = "update ".getSQLTable('choose')." set info='".json_encode($info)."' where gameid='".$userData->gameid."'";
		$conne->uidRst($sql);
		
	}while(false);
	
?> 