<?php 
	function getTecValue($level,$begin,$step){
		$v = $begin;
		for($i=1;$i<$level;$i++)
		{	
			$v += max(1,floor($step*$i));
		}
		return $v;
	}
	for($i=1;$i<100;$i++)
		echo $i.'-'.getTecValue($i,1,0.33).'<br/>';
?> 