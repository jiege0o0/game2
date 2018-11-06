<?php 
	$username = $msg->username;
	$serverid = $msg->serverid;
	$isadult = $msg->isadult;
	$time = $msg->time;
	$flag = $msg->flag;
	$key = 'hange0o0_login';


	$loginOK = md5($username.''.$serverid.''.$isadult.''.$time.''.$key) == $flag;		
	


?> 