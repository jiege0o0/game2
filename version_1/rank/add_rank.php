<?php 
// $rankType;
// $rankScore;
// $msg->gameid;
$time = time();
do{
	//���ϰ�������
	$sql = "update ".getSQLTable('rank_'.$rankType)." set score=".$rankScore.",time=".$time." where gameid='".$userData->gameid."'";
	$result = $conne->uidRst($sql);
	if(!$result)//û����ı���Сֵ�����ݣ������ֵС���Լ��ģ�
	{
		$sql = "update ".getSQLTable('rank_'.$rankType)." set score=".$rankScore.",time=".$time.",nick='".$userData->nick."',type=".$userData->type.",gameid='".$userData->gameid."' where score<".$rankScore." order by score,time desc limit 1";
		$conne->uidRst($sql);
	}
}while(false)
?> 