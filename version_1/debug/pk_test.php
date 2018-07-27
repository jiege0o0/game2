<?php 
$id=$msg->id;
$hangIndex=$userData->hang->level + 1;
$mapIndex = ceil($hangIndex/100);
require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/map".$mapIndex.".php");
require_once($filePath."cache/base.php");


do{		


	$returnData->data = getUserPKData2('0#5,0#65,28#271,264#44,332#295,408#313,528#76,900#6,1155#227,1182#220,1352#207');

}while(false);
function getUserPKData2($list){
		global $monster_base,$skill_base;
		
		
		$result = new stdClass();
		$result->list = array();
		$result->skill = array();
		
		
		$orgin = explode(",",$card);
			

		
		
		
		$mpList = getMPList();
		$stepCD = 50;
		$mpCost = 0;
		$list = explode(",",$list);
		$len = count($list);
		for($i=0;$i<$len;$i++)
        {
			$group = explode("#",$list[$i]);
			$time = $group[0]*$stepCD; 
			$id = $group[1];
			if($id < 200)//@skillID
			{
				$mpCost += $monster_base[$id]['cost'];
			}
			else
			{
				$mpCost += $skill_base[$id]['cost'];
				if($skill_base[$id]['sv4'] == -10001)
				{
					debug(join(",",$mpList));
					//debug($mpList[23]);
					addMPTime($mpList,$time + 3000 + $skill_base[$id]['cd']*1000,$skill_base[$id]['sv1'] + $skill_base[$id]['cost']);
					debug(join(",",$mpList));
					//debug($mpList[23]);
				}
			}
		
			if($mpList[$mpCost] > $time)//MP不够
			{
				// debug('======');
				// debug($mpCost);
				// debug($mpList[$mpCost]);
				// debug($time);
				$result->fail = 101;
				break;
			}	
			
			
			array_push($result->list,array(
				"mid"=>$id,
				"time"=>$time,
				"id"=>$i,
				));
		}
		
		//返还未使用的技能卡
		$len = count($orgin);
		for($i=0;$i<$len;$i++)
		{
			$id = $orgin[$i];
			if($id > 200)//@skillID
				array_push($result->skill,$id);
		}
		return $result;
	}

?> 