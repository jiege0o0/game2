<?php 
	//把数字变成1位的字符(最大值为9+26+26 = 61)
	function numToStr($num){
		$str = '';
		while($num)
		{
			$str = _numToStr($num%61).$str;
			$num = (int)($num/61);
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
	
	echo _numToStr(12);
?> 