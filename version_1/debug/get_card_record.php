<?php //请求以同步数据
require_once($filePath."tool/conn.php");
do{
	$arr = array('use_hang','use_fight_init','use_fight_get');
	foreach($arr as $key=>$value)
	{
		$file  = $dataFilePath.'log/'.$value.'.txt';
		$returnData->{$value} = file_get_contents($file);	
	}
	$sql = "select * from ".getSQLTable('card_like')." where like_num>0 or unlike_num>0";
	$returnData->like = $conne->getRowsArray($sql);
	
}while(false)
?> 