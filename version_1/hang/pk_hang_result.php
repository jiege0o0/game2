<?php 
$list=$msg->list;
$hangIndex=$userData->hang->level + 1;
require_once($filePath."pk/pk_tool.php");
require_once($filePath."cache/base.php");
do{		
	if($userData->pk_common->pktype != 'hang' || $userData->pk_common->level != $hangIndex)//最近不是打这个
	{
		$returnData -> fail = 1;
		break;
	}
	$pkData = $userData->pk_common->pkdata;
	
	$playerData = getUserPKData($list,$pkData->players[0]);
	if($playerData -> fail)//出怪顺序有问题
	{
		$returnData -> fail = $playerData -> fail;
		break;
	}
	$pkData->players[0] = $playerData;
	$pkData->players[1] = getAutoPKData($pkData->players[1]);
	
	require_once($filePath."pk/pk.php");
	if($returnData -> fail)//PK结果有问题
	{
		break;
	}
	
	$upProp = array(5,20,40,70,100,150,200,250,300,350,400,550,600,650,700,750,800,850,900);//19个
	$award = new stdClass();
	$award->props = array();
	$addCoin = 90+$hangIndex*10 + floor($hangIndex/5)*20;
	$userData->addCoin($addCoin);
	$award->coin = $addCoin;
	
	if(in_array($hangIndex,$upProp))
	{
		$award->props[101] = 1;
		$userData->addProp(101,1);
	}
	
	$award->props[102] = 1;
	$userData->addProp(102,1);
	
	$propArr = array();
	foreach($prop_base as $key=>$value)
	{
		if($value['hanglevel'] && $value['hanglevel']<=$hangIndex + 5)
		{
			array_push($propArr,$value);
		}
	}
	usort($propArr,"my_hang_sort");
	$addProp = $propArr[rand(0,2)];
	$num = max(1,$hangIndex - $addProp['hanglevel'] + 5);
	$award->props[$addProp['id']] = $num;
	$userData->addProp($addProp['id'],$num);
	
	$returnData->award = $award;
	

	$userData->hang->level = $hangIndex;
	if(!$userData->hang->awardtime)
		$userData->hang->awardtime = time();
	$userData->hang->pktime = time();
	$userData->setChangeKey('hang');
	$returnData->level = $userData->hang->level;
	$returnData->pktime = $userData->hang->pktime;
	

}while(false);

function my_hang_sort($a,$b)
{
	if ($a['hanglevel'] > $b['hanglevel'])
		return -1;
	if ($a['hanglevel'] < $b['hanglevel'])
		return 1;
	return 0;
}


?> 