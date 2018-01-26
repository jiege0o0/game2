<?php 
$otherid=$msg->otherid;
$master=$msg->gameid;
do{
	$sql = "update ".getSQLTable('slave')." set master=gameid,protime=".(time() + 10)." where gameid='".$otherid."' and master='".$master."'";
	$conne->uidRst($sql);
}while(false)
?> 