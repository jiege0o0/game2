<?php 
	$url = 'Location:http://120.77.153.203/game2_client/version1/index_qunhei.html?';
	$arr = array();
	foreach($_GET as $key=>$value)
	{
		array_push($arr,$key.'='.$value);
	}
	$url = $url.implode('&',$arr);
	header($url);
?> 