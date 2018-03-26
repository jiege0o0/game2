<?php 
$otherid=$msg->otherid;
$master=$msg->gameid;
$time = time();
do{
	$sql = "update ".getSQLTable('slave')." set master=gameid,protime=0 where gameid='".$otherid."' and master='".$master."'";
	$conne->uidRst($sql);
	
	$sql = "update ".getSQLTable('user_open')." set slavetime=".$time.",mailtime=".$time." where gameid='".$otherid."'";
	$conne->uidRst($sql);
	
	$oo = new stdClass();
	$oo->nick = base64_encode($userData->nick);
	$oo->type = $userData->type;
	$oo->head = $userData->head;
	$oo->rd = rand(0,9);
	$oo = json_encode($oo);
	$sql = "insert into ".getSQLTable('mail')."(from_gameid,to_gameid,type,content,time) values('".$userData->gameid."','".$otherid."',3,'".$oo."',".$time.")";
	$conne->uidRst($sql);
}while(false)
?> 