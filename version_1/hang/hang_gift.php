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
	$v1 = 1.2;
	$v2 = array(2.2,2,1.8);
	$v2 = $v2[$type-1];
	$base = 2;
	for($i=1;$i<$lv;$i++)
	{
		$base += pow($i+1,$v1)*$v2;
	}
	return floor($base);
}


do{
	$giftTimes = $userData->hang->giftnum;
	if(!$giftTimes)
		$giftTimes = 0;
	if($giftTimes >= 10)//未到领奖时间
	{
		$returnData -> fail = 1;
		break;
	}
	$giftTimes ++;
	
	$award = new stdClass();
	$award->props = array();
	$addCoin = getCoinNeed($giftTimes)*15;
	$userData->addCoin($addCoin);
	$award->coin = $addCoin;
	$addPropNum = getOtherNeed($giftTimes,1)*2;
	for($i=1;$i<=3;$i++)
	{
		$award->props[$i] = $addPropNum;
		$userData->addProp($i,$addPropNum);
	}

	$userData->hang->giftnum = $giftTimes;
	$userData->setChangeKey('hang');
	
	$returnData->award = $award;
	
}while(false)
?> 