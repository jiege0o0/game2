<?php 
require_once($filePath."cache/base.php"); 
do{
	$currentActive = null;
	$time = time();
	foreach($active_base as $key=>$value)
	{
		$start = strtotime($value['start']);
		$end = strtotime($value['end']);
		if($time >= $start && $time<= $end)
		{
			$currentActive = $value;
			$currentActive['start'] = $start;
			$currentActive['end'] = $end;
			break;
		}
	}
}while(false)

?> 