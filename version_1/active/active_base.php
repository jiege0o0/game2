<?php 
require_once($dataFilePath."active/active_list.php");
do{
	$time = time();
	while($active_base[0] && $active_base[0]['end'] < $time-24*3600)
	{
		array_shift($active_base);
	}
	$len = count($active_base);
	if($len < 4)
	{
		$base = 1540828800;//总的活动开始
		$cd = 48*3600;
		$active = array(1,2,3,4,5);
		foreach($active_base as $key=>$value)
		{
			$index = array_search($value['type'],$active);
			if($index === false || $index === null)
				continue;
			array_splice($active,$index,1);
		}
		shuffle($active);
		
		
		if($len)
			$nextBeginTime = $active_base[$len-1]['start'] + 48*3600;
		else
		{
			$num = floor(($time - $base)/$cd);//完成的场数
			$nextBeginTime = $base + $num*$cd + 6*3600;
		}
		$awardArr = array('box','skill','hero');
		while(count($active_base) < 3)
		{
			$type = array_shift($active);
			if($type == 2)
			{
				$questionIndex ++;
				$v1 = 'question'.$questionIndex;
			}
			else
				$v1 = '';
			array_push($active_base,array("start"=>$nextBeginTime,"end"=>$nextBeginTime + 42*3600,"type"=>$type,"faward"=>$awardArr[rand(0,2)],"v1"=>$v1));
			$nextBeginTime += 48*3600;
		}
		// array("id"=>1,"start"=>"2018-09-20 6:00:00","end"=>"2018-09-21 24:00:00","type"=>1,"faward"=>"box","v1"=>"","v2"=>"","v3"=>"")
		
		$list = array();
		foreach($active_base as $key=>$value)
		{	
			$temp = array();
			foreach($value as $key2=>$value2)
			{
				if(is_string($value2))
					array_push($temp,'"'.$key2.'"=>"'.$value2.'"');
				else
					array_push($temp,'"'.$key2.'"=>'.$value2.'');
			}
			array_push($list,'array('.join(",",$temp).')');
		}
		$str = '<?php'.PHP_EOL.'$active_base = array('.join(",",$list).');'.PHP_EOL.'$questionIndex='.$questionIndex.';'.PHP_EOL.'?>';
		
		$file = $dataFilePath."active/active_list.php";
		file_put_contents($file, $str);
	}
	
	
}while(false)

?> 