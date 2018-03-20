<?php 
$pkdata = $msg->pkdata;
$otherid = $msg->otherid;
$oo = json_encode($pkdata);
$sql = "insert into ".getSQLTable('pk_recode')."(gameid,pkdata,time) values('".$otherid."','".$oo."',".time().")";
$conne->uidRst($sql);
debug($sql);
?> 