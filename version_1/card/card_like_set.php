<?php 
require_once($filePath."cache/base.php");
$id = $msg->id;
$like = $msg->like;
do{
	// if(!$userData->active->like_time || !isSameDate($userData->active->like_time))
	// {
		// $userData->active->like_obj = new stdClass();
	// }
	
	// if($userData->active->like_obj->{$id})//投过票
	// {
		// $returnData -> fail = 1;
		// break;
	// }
	
	// $count = 0;
	// foreach($userData->active->like_obj as $key=>$value)
		// $count ++;
	
	// if($count >= 10)//投票上限
	// {
		// $returnData -> fail = 2;
		// break;
	// }
	// $canSet = $skill_base[$id]['level'] == 0 || $userData->card->skill->{$id} || in_array($id,$userData->card->monster);
	// if(!$canSet)
	// {
		// $returnData -> fail = 3;
		// break;
	// }
	
	if($like == 1)
		$sql = "update  ".getSQLTable('card_like')." set like_num=like_num+1 where id=".$id;
	else 
		$sql = "update  ".getSQLTable('card_like')." set unlike_num=unlike_num+1 where id=".$id;
	$result = $conne->uidRst($sql);
	
	// $userData->active->like_obj->{$id} = $like;
	// $userData->active->like_time = time();
	// $userData->setChangeKey('active');
	// $returnData -> like_time = $userData->active->like_time;
	// $returnData -> like_obj = $userData->active->like_obj;
	
}while(false)

?> 