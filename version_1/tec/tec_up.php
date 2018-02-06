<?php
require_once($filePath."cache/base.php"); 

//升到该级需要的金币
function getCoinNeed($lv){
	$v1 = 2;
	$v2 = 30;
	$v3 = 50;
	$base = 50;
	for($i=1;$i<$lv;$i++)
	{
		$base += pow($i+1,$v1)*$v2 - ($i+1)*$v3;
	}
	return $base;
}

//升到该级需要的资源 type:1-3
function getOtherNeed($lv,$type){
	$v1 = 1.1;
	$v2 = array(1.8,1.5,1.2);
	$v2 = $v2[$type-1];
	$base = 2;
	for($i=1;$i<$lv;$i++)
	{
		$base += pow($i+1,$v1)*$v2;
	}
	return floor($base);
}

	
$id=$msg->id;
$lv = $userData->getTecLevel($id);
$vo = $tec_base[$id];
$coin = getCoinNeed($lv + $vo['coinlv'] + $vo['step']*$lv); 
$arr = array();
$idAdd = 0;
if($vo['type'] == 1)//通用类型需要的道具会变化
{
	$idAdd += $lv - 1;
	if($vo['id'] == 1)
		array_push($arr,array('id'=>101,'num'=>1));
}
if($vo['prop1'])
	array_push($arr,array('id'=>$vo['prop1']+ $idAdd,'num'=>getOtherNeed($lv,1)));
if($vo['prop2'])
	array_push($arr,array('id'=>$vo['prop2']+ $idAdd,'num'=>getOtherNeed($lv,2)));
if($vo['prop3'])
	array_push($arr,array('id'=>$vo['prop3']+ $idAdd,'num'=>getOtherNeed($lv,3)));



do{
	if($vo['type'] == 1 && $id != 1)
	{
		if($userData->getTecLevel(1) < $userData->getTecLevel($id) + $vo['level'])
		{
			$returnData -> fail = 3;
			$returnData -> level = $userData->getTecLevel($id);
			break;
		}
	}

	if($userData->getCoin() < $coin)
	{
		$returnData -> fail = 1;
		$returnData -> level = $userData->getTecLevel($id);
		$returnData->sync_coin = $userData->coin;
		break;
	}
	$userData->addCoin(-$coin);
	
	foreach($arr as $key=>$value)
	{
		if($userData->getPropNum($value['id']) < $value['num'])
		{
			$returnData -> fail = 2;
			$returnData -> level = $userData->getTecLevel($id);
			$returnData->sync_prop = new stdClass();
			$returnData->sync_prop->{$value['id']} = $userData->getPropNum($value['id']);
			break;
		}
		$userData->addProp($value['id'],-$value['num']);
	}
	
	if($returnData -> fail)
		break;
	
	$userData->levelUpTec($id);	
}while(false)


?> 