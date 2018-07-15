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

//升级科技
function levelUpTec($id){
	global $returnData,$tec_base,$userData,$conne,$filePath;
	$userData->tec->{$id} = $userData->getTecLevel($id) + 1;
	$userData->setChangeKey('tec');
	
	if(!$returnData->{'sync_tec'})
	{
		$returnData->{'sync_tec'} = new stdClass();
	}
	$returnData->{'sync_tec'}->{$id} = $userData->tec->{$id};
	
	//重置每小时金币
	$vo = $tec_base[$id];
	if($vo['type'] == 2)
		resetForce();
	else if($vo['type'] == 3)
		resetHourCoin();
	else if($id == 1)//主城等级
	{
		$userData->level = $userData->tec->{$id};
		$userData->setChangeKey('level');	
		require($filePath."slave/slave_reset_list.php");
	}	
}

//受科技影响
function resetHourCoin(){
	global $returnData,$tec_base,$rankType,$rankScore,$filePath,$conne,$userData;
	$force = 10;
	foreach($tec_base as $key=>$value)
	{
		if($value['type'] == 3)
		{
			$level = $userData->getTecLevel($key);
			if($level)
			{
				$addValue = getTecValue($level,$value['value1'],20);
				$force += $addValue;
			}
		}
	}
	$userData->hourcoin = $force;
	$returnData->sync_hourcoin = $force;
	$userData->setChangeKey('hourcoin');
	
	$rankType = 'hourcoin';
	$rankScore = $userData->hourcoin;
	require($filePath."rank/add_rank.php");
	require($filePath."slave/slave_reset_list.php");
	
}

//受科技影响
function resetForce(){
	global $returnData,$tec_base,$rankType,$rankScore,$filePath,$conne,$userData;
	$force = 0;
	foreach($tec_base as $key=>$value)
	{
		if($value['type'] == 2)
		{
			$level = $userData->getTecLevel($key);
			if($level)
			{
				$addValue = getTecValue($level,$value['value1'],0.3);
				$force += $addValue;
			}
		}
	}
	$userData->tec_force = $force;
	$returnData->sync_tec_force = $force;
	$userData->setChangeKey('tec_force');
	
	$rankType = 'force';
	$rankScore = $userData->tec_force;
	require($filePath."rank/add_rank.php");
	require($filePath."slave/slave_reset_list.php");
}

function getTecValue($level,$begin,$step){
	$v = $begin;
	for($i=1;$i<$level;$i++)
	{	
		$v += max(1,floor($step*$i));
	}
	return $v;
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
	
	levelUpTec($id);	
}while(false)


?> 