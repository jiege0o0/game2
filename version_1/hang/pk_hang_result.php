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
	
	$playerData = getUserPKData($list,$pkData->players[0],$msg->cd,$msg->key);
	$enempList = $pkData->players[1]->autolist;
	if($playerData -> fail)//出怪顺序有问题
	{
		$returnData -> fail = $playerData -> fail;
		break;
	}
	
	$upProp = array();//19个
	for($i=4;$i<=22;$i++)
	{
		array_push($upProp,$prop_base[$i]['hanglevel']);
	}
	$award = new stdClass();
	$award->props = array();
	$addCoin = 90+$hangIndex*10 + floor($hangIndex/5)*20;
	$userData->addCoin($addCoin);
	$award->coin = $addCoin;
	
	if(in_array($hangIndex,$upProp) && $userData->getPropNum(101) == 0)
	{
		$award->props[101] = 1;
		$userData->addProp(101,1);
	}
	
	// if(!$award->props[101] && $hangIndex%2 == 0)
	// {
		// $award->props[102] = 1;
		// $userData->addProp(102,1);
	// }
	
	
	
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
	$userData->hang->lastlist = $enempList;
	$userData->setChangeKey('hang');
	$returnData->level = $userData->hang->level;
	$returnData->pktime = $userData->hang->pktime;
	$returnData->lastlist = $userData->hang->lastlist;
	
	//入榜
	$rankType = 'hang';
	$rankScore = $hangIndex;
	require($filePath."rank/add_rank.php");
	
	if($userData->hang->level > 10)
	{
		//入录像
		$info = new stdClass();
		$info->gameid = $userData->gameid;
		$info->nick = base64_encode($userData->nick);
		$info->type = $userData->type;
		$info->head = $userData->head;
		$info->force = $playerData->force;
		$info->cd = $msg->cd;
		$data->version = $pk_version;

		$data = new stdClass();
		$data->pkdata = $pkData;
		$data->pklist = $list;

		
		$sql = "update ".getSQLTable('video')." set info='".json_encode($info)."',data='".json_encode($data)."',time=".time()." where level=".($userData->hang->level)." order by time limit 1";
		$conne->uidRst($sql);
	}
	
	
	

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