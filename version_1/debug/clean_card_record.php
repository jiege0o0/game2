<?php //请求以同步数据
require_once($filePath."tool/conn.php");
do{
	if($msg->key != 'hange0o0')
	{
		$returnData->fail = 1;
		break;
	}
	$arr = array('use_hang','use_fight_init','use_fight_get');
	foreach($arr as $key=>$value)
	{
		$file  = $dataFilePath.'log/'.$value.'.txt';
		file_put_contents($file,'');	
	}
	$sql = "update ".getSQLTable('card_like')." set like_num=0,unlike_num=0";
	$conne->uidRst($sql);
	$returnData->success=1;
}while(false)
?> 