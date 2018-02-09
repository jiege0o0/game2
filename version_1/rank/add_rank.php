<?php 
// $rankType;
// $rankScore;
// $msg->gameid;
$time = time();
do{
	//更上榜上数据
	$sql = "update ".getSQLTable('rank_'.$rankType)." set score=".$rankScore.",time=".$time.",head='".$userData->head."' where gameid='".$userData->gameid."'";
	// debug($sql);
	$result = $conne->uidRst($sql);
	if(!$result)//没有则改变最小值的数据（如果该值小于自己的）
	{
		$sql = "update ".getSQLTable('rank_'.$rankType)." set score=".$rankScore.",time=".$time.",nick='".$userData->nick."',type=".$userData->type.",gameid='".$userData->gameid."',head='".$userData->head."' where score<".$rankScore." order by score,time desc limit 1";
		$conne->uidRst($sql);
	}
}while(false)
?> 