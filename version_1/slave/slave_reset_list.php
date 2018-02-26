<?php 
do{
	$sql = "update ".getSQLTable('slave')." set head='".$userData->head."',hourcoin=".$userData->hourcoin.",tec_force=".$userData->tec_force.",level=".$userData->level." where gameid='".$userData->gameid."'";
	$conne->uidRst($sql);
}while(false)
?> 