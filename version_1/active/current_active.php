<?php 
require_once($filePath."active/active_base.php"); 
do{
	$currentActive = null;
	$time = time();
	foreach($active_base as $key=>$value)
	{
		// $start = strtotime($value['start']);
		// $end = strtotime($value['end']);
		
		$start = ($value['start']);
		$end = ($value['end']);
		if($time >= $start && $time<= $end)
		{
			$currentActive = $value;
			break;
		}
	}
	
}while(false)

?> 