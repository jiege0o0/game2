<?php
	date_default_timezone_set("PRC"); 
	//打印Debug到客户端
	function debug($str){
		global $debugC,$debugArr;
		if(!$debugC)
			return;
		array_push($debugArr,$str);
	}
	
	$testTime = array();
	function startTestTime($key){
		global $testTime;
		if(!$testTime[$key])
		{
			$testTime[$key] = array('start'=>0,'total'=>0,'count'=>0);
		}
		$testTime[$key]['start'] = microtime(true);
	}
	function stopTestTime($key){
		global $testTime;
		if(!$testTime[$key])
		{
			return;
		}
		
		$testTime[$key]['total'] += microtime(true) - $testTime[$key]['start'];
		$testTime[$key]['count'] ++;
	}
	
	//直接输出到网页
	function trace($v,$isSimple=false){
		//return;

		echo '<br/>';
		if($isSimple)
			echo ($v);
		else
			echo json_encode($v);
			// echo json_encode(var_dump($v);
		echo '<br/>';
	}
	
	//写出错日志
	function errorLog($str){
		global $dataFilePath,$serverID;
		$file  = $dataFilePath.'log/server'.$serverID.'/log'.date('Ymd', time()).'.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
		file_put_contents($file, date('h:i:sa', time()).'|'.time()." : ".$str.PHP_EOL,FILE_APPEND);
	}
	
	//玩家的消息日志
	function userLog($gameid,$str){
		global $dataFilePath,$serverID;
		$file  = $dataFilePath.'userlog/log_'.$gameid."#".date('Ymd', time()).'.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
		file_put_contents($file, date('Y-m-d h:i:sa', time())." : ".$str.PHP_EOL,FILE_APPEND);
	}
	//玩家的行为日志
	function userLog2($gameid,$str){
		global $dataFilePath,$serverID;
		$file  = $dataFilePath.'userlog2/'.$gameid.'.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
		file_put_contents($file, date('Y-m-d h:i:sa', time())." : ".$str.PHP_EOL,FILE_APPEND);
	}
	function clientLog($str){
		global $dataFilePath,$serverID;
		$file  = $dataFilePath.'log/client/log'.date('Ymd', time()).'.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
		file_put_contents($file, date('h:i:sa', time()).'|'.time()." : ".$str.PHP_EOL,FILE_APPEND);
	}
	//写消费日志
	function payLog($str){
		global $dataFilePath,$serverID;
		$file  = $dataFilePath.'log/server'.$serverID.'/pay_log'.date('Ymd', time()).'.txt';//要写入文件的文件名（可以是任意文件名），如果文件不存在，将会创建一个
		file_put_contents($file, date('h:i:sa', time())." : ".$str.PHP_EOL,FILE_APPEND);
	}
	
	//返回到客户端的数据处理
	function sendToClient($returnData){
		$returnData->server_time = time();
		echo json_encode($returnData);
	}
	
	//把数字变成1位的字符(最大值为9+26+26 = 61)
	function numToStr($num){
		if(!$num)
			return '0';
		$str = '';
		while($num)
		{
			$str = _numToStr($num%62).$str;
			$num = (int)($num/62);
		}
		return $str;
	}
	
	function _numToStr($num){
		if($num<10)
			return chr(48 + $num);
		$num -= 10;
		if($num<26)	
			return chr(65 + $num);
		$num -= 26;
		return chr(97 + $num);
	}
	
	//把1位的字符变成数字(最大值为9+26+26 = 61)
	function strToNum($str){
		$num = 0;
		$arr = str_split($str);
		$len = count($arr);
		for($i=0;$i<$len;$i++)
		{
			$num += pow(62,$len-$i-1)*_strToNum($arr[$i]);
		}
		return $num;
	}
	
	function _strToNum($str){
		$num = ord($str);
		if($num >= 97)
			return $num - 97 +26 + 10;
		if($num >= 65)
			return $num - 65 + 10;
		return $num - 48;
	}
	
	//cdKey相关============================
	function testCDKey($id,$key){
		
		$time = (int)substr($key,16) + 1453027182;
		if(abs($time - time())>3600)
		{
			return false;
		}
		return getCDKey($id,$time) == $key;
	}
	function getCDKey($id,$time){
		return substr(md5('hange0o0_'.$time.$id),16).($time - 1453027182);
	}
	
	//随机排序
	function randomSortFun($a,$b){
		return lcg_value()>0.5?1:-1;
	}
	
	//时间相关============================
	function isSameDate($t1,$t2=null){
		if(!$t2)
			$t2 = time();
		return date('Ymd', $t1) == date('Ymd', $t2);
	}
	
	//今天0点
	function todayZero(){
		//获取当天的年份
		$y = date("Y");
		 
		//获取当天的月份
		$m = date("m");
		 
		//获取当天的号数
		$d = date("d");
 
		//将今天开始的年月日时分秒，转换成unix时间戳(开始示例：2015-10-12 00:00:00)
		return mktime(0,0,0,$m,$d,$y);
	}

?>